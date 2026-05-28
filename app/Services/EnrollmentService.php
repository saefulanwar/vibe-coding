<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Enrollment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EnrollmentService
{
    protected MoodleService $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * Activate access for paid order
     */
    public function activateOrderAccess(Order $order): void
    {
        $user = $order->user;
        $batch = $order->courseBatch;
        $course = $batch->course;

        // 1. Create local enrollment record
        Enrollment::updateOrCreate(
            [
                'user_id' => $user->id,
                'course_batch_id' => $batch->id,
            ],
            [
                'enrolled_at' => now(),
                'expires_at' => null, // Default lifetime access
            ]
        );

        Log::info("Local enrollment activated for user {$user->email} on batch {$batch->name} of course {$course->title}");

        // 2. Moodle enrollment if source is moodle
        if ($course->source === 'moodle') {
            try {
                // Always verify the Moodle user actually exists on the LMS by email lookup.
                // A stored moodle_user_id can become stale if the account was deleted, recreated
                // with a different auth type, or if a previous creation attempt partially failed.
                $verifiedMoodleId = $this->moodleService->getMoodleUserByEmail($user->email);

                if ($verifiedMoodleId) {
                    // User exists on Moodle — update local record if it was stale or missing
                    if ($user->moodle_user_id !== $verifiedMoodleId) {
                        $user->update(['moodle_user_id' => $verifiedMoodleId]);
                        Log::info("Updated stale local moodle_user_id to {$verifiedMoodleId} for {$user->email}");
                    }
                } else {
                    // User does NOT exist on Moodle — create a new account
                    $moodlePassword = 'P@ssw0rd' . Str::random(4) . '!';
                    $verifiedMoodleId = $this->moodleService->createMoodleUser([
                        'email' => $user->email,
                        'name' => $user->name,
                        'password' => $moodlePassword,
                    ]);

                    $user->update(['moodle_user_id' => $verifiedMoodleId]);
                    Log::info("Created Moodle user ID {$verifiedMoodleId} for {$user->email}");
                }

                // Enroll user in Moodle Course
                if ($course->moodle_course_id && $course->moodle_course_id > 0) {
                    Log::info("Attempting enrollment: User {$verifiedMoodleId} -> Moodle Course {$course->moodle_course_id} (Course: {$course->title})");
                    $this->moodleService->enrollUserInCourse(
                        $verifiedMoodleId,
                        $course->moodle_course_id
                    );
                    Log::info("Enrolled Moodle user ID {$verifiedMoodleId} in Moodle course {$course->moodle_course_id}");

                    // Add user to Moodle Group if batch has moodle_group_id
                    if ($batch->moodle_group_id) {
                        $this->moodleService->addUserToGroup(
                            $batch->moodle_group_id,
                            $verifiedMoodleId
                        );
                        Log::info("Added Moodle user ID {$verifiedMoodleId} to Moodle Group ID {$batch->moodle_group_id}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed auto-enrollment to Moodle during webhook: " . $e->getMessage());
            }
        }
    }
}
