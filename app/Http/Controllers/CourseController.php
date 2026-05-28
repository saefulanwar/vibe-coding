<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Enrollment;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    protected MoodleService $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * Display student learning dashboard with purchased courses and all catalog
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Fetch enrolled batches
        $enrolledBatches = $user->enrolledBatches()
            ->with(['course.category', 'course.modules.lessons'])
            ->get();
        
        // Fetch available batches (where user is not enrolled)
        $enrolledBatchIds = $enrolledBatches->pluck('id')->toArray();
        $availableBatches = CourseBatch::whereHas('course', function ($query) {
                $query->where('is_published', true);
            })
            ->with(['course.category'])
            ->withCount('enrollments')
            ->whereNotIn('id', $enrolledBatchIds)
            ->get();

        // Fetch user's completed certificates
        $certificates = \App\Models\Certificate::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get()
            ->keyBy('course_id');

        return view('dashboard', compact('enrolledBatches', 'availableBatches', 'certificates'));
    }

    /**
     * Start learning course (Local content or Seamless Moodle SSO)
     */
    public function startLearning(Course $course)
    {
        $user = Auth::user();

        // Check if student has access to any batch of this course
        $enrollment = Enrollment::where('user_id', $user->id)
            ->whereIn('course_batch_id', $course->batches()->pluck('id'))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$enrollment) {
            return redirect()->route('dashboard')->with('error', 'Anda belum terdaftar atau tidak memiliki akses ke kursus ini.');
        }

        // Check if batch is already started
        $batch = $enrollment->courseBatch;
        if (now() < $batch->start_date) {
            return redirect()->route('dashboard')->with('error', 'Kelas belum dimulai. Kelas dimulai pada: ' . $batch->start_date->format('d M Y H:i'));
        }

        // 1. If course is Lokal, render local learning viewer (Courses -> Modules -> Lessons)
        if ($course->source === 'local') {
            $course->load(['modules.lessons']);
            $firstLesson = $course->modules->first()?->lessons->first();
            
            if (!$firstLesson) {
                return redirect()->route('dashboard')->with('error', 'Kursus lokal ini belum memiliki materi pembelajaran.');
            }

            return redirect()->route('lessons.show', [
                'course' => $course->id,
                'lesson' => $firstLesson->id
            ]);
        }

        // 2. If course is Moodle, execute Seamless SSO redirect
        if ($course->source === 'moodle') {
            try {
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $user->email)[0]));
                
                // Self-healing: Always verify Moodle user exists and is enrolled (handles stale IDs)
                $this->createAndEnrollMoodleUser($user, $course, $enrollment->courseBatch);

                // Get SSO login URL (falls back to direct course URL if auth_userkey is unavailable)
                $ssoLoginUrl = $this->moodleService->getSsoLoginUrl($username, $user->email, $course->moodle_course_id);

                // Redirect student to Moodle Classroom
                return redirect()->away($ssoLoginUrl);
            } catch (\Exception $e) {
                Log::error("SSO Moodle failed for {$user->email}: " . $e->getMessage());
                return redirect()->route('dashboard')->with('error', 'Gagal memproses Single Sign-On ke LMS Moodle: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard')->with('error', 'Sumber kursus tidak dikenal.');
    }

    /**
     * Helper to create and enroll user in Moodle on-the-fly (Self-healing)
     * Always verifies the Moodle user exists by email lookup before enrolling.
     */
    protected function createAndEnrollMoodleUser($user, $course, $batch): void
    {
        // Verify if user already exists on Moodle by email lookup
        $moodleUserId = $this->moodleService->getMoodleUserByEmail($user->email);

        if ($moodleUserId) {
            Log::info("Self-healing: Found existing Moodle user ID {$moodleUserId} for {$user->email}");
        } else {
            // User does NOT exist on Moodle — create a new account
            $moodlePassword = 'P@ssw0rd' . \Illuminate\Support\Str::random(4) . '!';
            $moodleUserId = $this->moodleService->createMoodleUser([
                'email' => $user->email,
                'name' => $user->name,
                'password' => $moodlePassword,
            ]);
            Log::info("Self-healing: Created Moodle user ID {$moodleUserId} for {$user->email}");
        }

        // Update local record
        $user->update(['moodle_user_id' => $moodleUserId]);

        // Enroll user in Moodle Course
        if ($course->moodle_course_id) {
            $this->moodleService->enrollUserInCourse(
                $moodleUserId,
                $course->moodle_course_id
            );
            Log::info("Self-healing: Enrolled Moodle user ID {$moodleUserId} in course {$course->moodle_course_id}");

            // Add user to Moodle Group if batch has moodle_group_id
            if ($batch->moodle_group_id) {
                $this->moodleService->addUserToGroup(
                    $batch->moodle_group_id,
                    $moodleUserId
                );
                Log::info("Self-healing: Added Moodle user ID {$moodleUserId} to Group ID {$batch->moodle_group_id}");
            }
        }
    }

    /**
     * Show local lesson content
     */
    public function showLocalLesson(Course $course, $lessonId)
    {
        $user = Auth::user();
        
        // Verify access to a batch of this course
        $enrollment = Enrollment::where('user_id', $user->id)
            ->whereIn('course_batch_id', $course->batches()->pluck('id'))
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$enrollment) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke materi ini.');
        }

        // Check if batch is already started
        $batch = $enrollment->courseBatch;
        if (now() < $batch->start_date) {
            return redirect()->route('dashboard')->with('error', 'Kelas belum dimulai. Kelas dimulai pada: ' . $batch->start_date->format('d M Y H:i'));
        }

        $course->load(['modules.lessons']);
        $currentLesson = null;
        
        foreach ($course->modules as $module) {
            foreach ($module->lessons as $lesson) {
                if ($lesson->id == $lessonId) {
                    $currentLesson = $lesson;
                    break 2;
                }
            }
        }

        if (!$currentLesson) {
            abort(404, 'Lesson not found');
        }

        return view('lessons.show', compact('course', 'currentLesson'));
    }

    /**
     * Store course review by student
     */
    public function storeReview(Request $request, Course $course)
    {
        $user = Auth::user();

        // Security Check (Skenario 1): Check if student has completed the course (completed certificate)
        $hasCompleted = \App\Models\Certificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->exists();

        if (!$hasCompleted) {
            abort(403, 'Anda harus menyelesaikan kursus ini terlebih dahulu sebelum memberikan ulasan.');
        }

        // Data Integrity (Skenario 2): Validate rating and sanitization
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        // Sanitization against XSS
        $reviewText = isset($validated['review_text']) ? strip_tags($validated['review_text']) : null;

        // Check if user has already reviewed
        $existingReview = \App\Models\CourseReview::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk kursus ini.');
        }

        // Save review
        \App\Models\CourseReview::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'rating' => $validated['rating'],
            'review_text' => $reviewText,
            'status' => 'published', // default status
        ]);

        return redirect()->back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
