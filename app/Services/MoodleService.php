<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class MoodleService
{
    protected ?string $baseUrl = null;
    protected ?string $token = null;

    public function __construct()
    {
        $this->baseUrl = config('services.moodle.url', '');
        $this->token = config('services.moodle.token', '');
    }

    /**
     * Call Moodle REST Web Service
     */
    protected function call(string $function, array $params = []): array
    {
        if (empty($this->token)) {
            Log::warning('Moodle token is not configured.');
            return [];
        }

        $url = rtrim($this->baseUrl, '/') . '/webservice/rest/server.php';

        try {
            $postData = array_merge([
                'wstoken' => $this->token,
                'wsfunction' => $function,
                'moodlewsrestformat' => 'json',
            ], $postDataParams = $this->buildNestedParams($params));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if (curl_errno($ch)) {
                $errorMsg = curl_error($ch);
                curl_close($ch);
                throw new Exception("Curl error calling {$function}: " . $errorMsg);
            }
            curl_close($ch);

            if ($httpStatus < 200 || $httpStatus >= 300) {
                throw new Exception("HTTP request failed with status: " . $httpStatus);
            }

            $data = json_decode($response, true);

            // Moodle API errors return exception key
            if (is_array($data) && isset($data['exception'])) {
                throw new Exception("Moodle API Exception: " . ($data['message'] ?? 'Unknown error') . " (" . ($data['errorcode'] ?? '') . ")");
            }

            return is_array($data) ? $data : [$data];
        } catch (Exception $e) {
            Log::error("Moodle API Error calling {$function}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper to build nested query params recursively if needed, or return flat array
     */
    protected function buildNestedParams(array $params): array
    {
        return $params;
    }

    /**
     * Get Moodle User by email
     */
    public function getMoodleUserByEmail(string $email): ?int
    {
        try {
            // Using core_user_get_users instead of core_user_get_users_by_field to prevent Access Control Exceptions.
            // Use the original email directly — Moodle API accepts any domain (restriction is UI-only).
            $response = $this->call('core_user_get_users', [
                'criteria' => [
                    [
                        'key' => 'email',
                        'value' => $email
                    ]
                ]
            ]);

            if (!empty($response['users']) && isset($response['users'][0]['id'])) {
                return (int) $response['users'][0]['id'];
            }
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'invalidrecordunknown') || str_contains($e->getMessage(), 'missing')) {
                return null;
            }
            throw $e;
        }

        return null;
    }


    /**
     * Create user in Moodle
     */
    public function createMoodleUser(array $userData): int
    {
        // Check if user already exists in Moodle first to prevent duplicates/errors
        $existingId = $this->getMoodleUserByEmail($userData['email']);
        if ($existingId !== null) {
            Log::info("User {$userData['email']} already exists in Moodle with ID {$existingId}");
            return $existingId;
        }

        // Format username to comply with Moodle rules (lowercase, alphanumeric, no special characters)
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $userData['email'])[0]));

        // Use the original email directly — Moodle API accepts any email domain.
        // Domain restrictions (e.g. @student.uny.ac.id) are enforced only on the Moodle UI/self-registration,
        // NOT on the Web Service API. Using the original email ensures that when the user logs in
        // via Google OAuth2, Moodle can match the email and link the accounts automatically.
        $params = [
            'users' => [
                [
                    'username' => $username,
                    'password' => $userData['password'] ?? 'P@ssw0rd123!',
                    'firstname' => $userData['firstname'] ?? explode(' ', $userData['name'])[0] ?? 'Student',
                    'lastname' => $userData['lastname'] ?? explode(' ', $userData['name'])[1] ?? 'Elearning',
                    'email' => $userData['email'],
                    'auth' => 'manual',
                    'idnumber' => $username,
                ]
            ]
        ];

        $response = $this->call('core_user_create_users', $params);

        if (!empty($response) && isset($response[0]['id'])) {
            return (int) $response[0]['id'];
        }

        throw new Exception('Failed to create Moodle user: Invalid API response.');
    }

    /**
     * Enroll user in Moodle Course
     */
    public function enrollUserInCourse(int $moodleUserId, int $moodleCourseId, int $roleId = 5): bool
    {
        if ($moodleCourseId <= 0) {
            Log::error("Cannot enroll: Invalid moodle_course_id ({$moodleCourseId}) for user {$moodleUserId}");
            throw new Exception("Invalid Moodle course ID: {$moodleCourseId}");
        }

        Log::info("Enrolling Moodle user {$moodleUserId} in course {$moodleCourseId} with role {$roleId}");

        $params = [
            'enrolments' => [
                [
                    'roleid' => $roleId, // 5 = Student
                    'userid' => $moodleUserId,
                    'courseid' => $moodleCourseId,
                ]
            ]
        ];

        $this->call('enrol_manual_enrol_users', $params);
        return true;
    }

    /**
     * Create a new Group in Moodle Course
     */
    public function createMoodleGroup(int $moodleCourseId, string $groupName, string $description = ''): int
    {
        $params = [
            'groups' => [
                [
                    'courseid' => $moodleCourseId,
                    'name' => $groupName,
                    'description' => $description,
                ]
            ]
        ];

        // For local testing, if Moodle URL or Token is not fully configured, return a mock ID
        try {
            $response = $this->call('core_group_create_groups', $params);

            if (!empty($response) && isset($response[0]['id'])) {
                return (int) $response[0]['id'];
            }
        } catch (Exception $e) {
            if (config('app.env') === 'local') {
                Log::warning('Moodle API core_group_create_groups failed in local environment. Returning mock group ID.');
                return rand(1000, 9999);
            }
            throw $e;
        }

        throw new Exception('Failed to create Moodle group: Invalid API response.');
    }

    /**
     * Add User to Moodle Group
     */
    public function addUserToGroup(int $moodleGroupId, int $moodleUserId): bool
    {
        $params = [
            'members' => [
                [
                    'groupid' => $moodleGroupId,
                    'userid' => $moodleUserId,
                ]
            ]
        ];

        try {
            $this->call('core_group_add_group_members', $params);
        } catch (Exception $e) {
            if (config('app.env') === 'local') {
                Log::warning('Moodle API core_group_add_group_members failed in local environment. Proceeding anyway.');
                return true;
            }
            throw $e;
        }
        
        return true;
    }

    /**
     * Request Single Sign-On One-Time Login URL from Moodle.
     * 
     * Attempts auth_userkey once. If it fails (Moodle UNY does not support userkey
     * for manual/oauth2 auth types), immediately falls back to the direct course URL.
     * Moodle will show its native login page with Google OAuth2 button, then redirect
     * the user to the course after successful authentication.
     */
    public function getSsoLoginUrl(string $username, ?string $userEmail = null, ?int $moodleCourseId = null): string
    {
        // Build the direct fallback URL (Moodle course page or dashboard)
        $fallbackUrl = $moodleCourseId 
            ? rtrim($this->baseUrl, '/') . "/course/view.php?id=" . $moodleCourseId 
            : rtrim($this->baseUrl, '/') . "/my/";

        // Use the original email directly — Moodle API accepts any domain
        $email = $userEmail ?? $username . '@student.uny.ac.id';

        try {
            // Single attempt: Call Moodle auth_userkey_request_login_url
            $response = $this->call('auth_userkey_request_login_url', [
                'user' => [
                    'username' => $username,
                    'email' => $email,
                    'idnumber' => $username
                ]
            ]);

            if (isset($response['loginurl'])) {
                return $response['loginurl'];
            }

            if (isset($response[0]['loginurl'])) {
                return $response[0]['loginurl'];
            }

            // loginurl not in response — use fallback
            Log::warning("Moodle auth_userkey response missing loginurl for {$username}, redirecting to course page.");
            return $fallbackUrl;
        } catch (Exception $e) {
            // auth_userkey is not supported on this Moodle instance — redirect directly to course page.
            // Moodle will show its native login page with Google OAuth2 button.
            Log::info("Moodle auth_userkey unavailable for {$username}, redirecting to course page: " . $e->getMessage());
            return $fallbackUrl;
        }
    }

    /**
     * Fetch Moodle Courses for Sync
     */
    public function getMoodleCourses(): array
    {
        return $this->call('core_course_get_courses');
    }
}
