<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Facades\Log;

class MoodleWebhookController extends Controller
{
    public function courseCompleted(Request $request)
    {
        try {
            $validated = $request->validate([
                'moodle_user_id' => 'required|integer',
                'moodle_course_id' => 'required|integer',
            ]);

            $user = User::where('moodle_user_id', $validated['moodle_user_id'])->first();
            $course = Course::where('moodle_course_id', $validated['moodle_course_id'])->first();

            if (!$user || !$course) {
                return response()->json(['message' => 'User or Course not found in local system.'], 404);
            }

            // Check if certificate already exists
            $existingCert = Certificate::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if ($existingCert) {
                return response()->json(['message' => 'Certificate already requested.'], 200);
            }

            $certificate = Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'unit_id' => $course->unit_id,
                'student_name_snapshot' => $user->name,
                'course_title_snapshot' => $course->title,
                'status' => 'pending',
                'completed_at' => now(),
            ]);

            return response()->json(['message' => 'Certificate queued successfully', 'data' => $certificate], 201);
        } catch (\Exception $e) {
            Log::error('MoodleWebhook Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
