<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Services\MoodleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Fetch enrolled courses
        $enrolledCourses = $user->enrolledCourses()->get();
        
        // Fetch other available courses
        $enrolledCourseIds = $enrolledCourses->pluck('id')->toArray();
        $availableCourses = Course::where('is_published', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->get();

        return view('dashboard', compact('enrolledCourses', 'availableCourses'));
    }

    /**
     * Start learning course (Local content or Seamless Moodle SSO)
     */
    public function startLearning(Course $course)
    {
        $user = Auth::user();

        // Check if student has access
        $hasAccess = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('dashboard')->with('error', 'Anda belum terdaftar atau tidak memiliki akses ke kursus ini.');
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
                // Formatting username similarly to Moodle account creation
                $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $user->email)[0]));
                
                // Get one-time login SSO link from Moodle Web Service
                $ssoLoginUrl = $this->moodleService->getSsoLoginUrl($username);

                // Seamlessly redirect student to Moodle Classroom
                return redirect()->away($ssoLoginUrl);
            } catch (\Exception $e) {
                return redirect()->route('dashboard')->with('error', 'Gagal memproses Single Sign-On ke LMS Moodle: ' . $e->getMessage());
            }
        }

        return redirect()->route('dashboard')->with('error', 'Sumber kursus tidak dikenal.');
    }

    /**
     * Show local lesson content
     */
    public function showLocalLesson(Course $course, $lessonId)
    {
        $user = Auth::user();
        
        // Verify access
        $hasAccess = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->exists();

        if (!$hasAccess) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke materi ini.');
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
}
