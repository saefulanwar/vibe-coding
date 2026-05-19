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
                // Ensure Moodle user account exists
                if (empty($user->moodle_user_id)) {
                    // Check if user already exists in Moodle by email to avoid duplication
                    $moodleUserId = $this->moodleService->getMoodleUserByEmail($user->email);
                    
                    if (!$moodleUserId) {
                        $moodlePassword = 'P@ssw0rd' . Str::random(4) . '!';
                        
                        // Create Moodle Account using custom Glacier API
                        $moodleUserId = $this->moodleService->createMoodleUser([
                            'email' => $user->email,
                            'name' => $user->name,
                            'password' => $moodlePassword,
                        ]);
                        Log::info("Created Moodle user ID {$moodleUserId} for {$user->email} via Glacier API");
                    } else {
                        Log::info("Found existing Moodle user ID {$moodleUserId} for {$user->email}");
                    }

                    $user->update(['moodle_user_id' => $moodleUserId]);
                }

                // Enroll user in Moodle Course
                if ($course->moodle_course_id) {
                    $this->moodleService->enrollUserInCourse(
                        $user->moodle_user_id,
                        $course->moodle_course_id
                    );
                    Log::info("Enrolled Moodle user ID {$user->moodle_user_id} in Moodle course {$course->moodle_course_id}");

                    // [BARU] Add user to Moodle Group if batch has moodle_group_id
                    if ($batch->moodle_group_id) {
                        $this->moodleService->addUserToGroup(
                            $batch->moodle_group_id,
                            $user->moodle_user_id
                        );
                        Log::info("Added Moodle user ID {$user->moodle_user_id} to Moodle Group ID {$batch->moodle_group_id}");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed auto-enrollment to Moodle during webhook: " . $e->getMessage());
            }
        }
    }
}
