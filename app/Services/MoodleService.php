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
            $response = Http::asForm()->post($url, array_merge([
                'wstoken' => $this->token,
                'wsfunction' => $function,
                'moodlewsrestformat' => 'json',
            ], $params));

            if ($response->failed()) {
                throw new Exception("HTTP request failed with status: " . $response->status());
            }

            $data = $response->json();

            // Moodle API errors return exception key
            if (is_array($data) && isset($data['exception'])) {
                throw new Exception("Moodle API Exception: " . ($data['message'] ?? 'Unknown error'));
            }

            return is_array($data) ? $data : [$data];
        } catch (Exception $e) {
            Log::error("Moodle API Error calling {$function}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create user in Moodle
     */
    public function createMoodleUser(array $userData): int
    {
        // Format username to comply with Moodle rules (lowercase, alphanumeric, no special characters)
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $userData['email'])[0]));
        
        $params = [
            'users' => [
                [
                    'username' => $username,
                    'password' => $userData['password'] ?? 'P@ssw0rd123!',
                    'firstname' => $userData['firstname'] ?? explode(' ', $userData['name'])[0] ?? 'Student',
                    'lastname' => $userData['lastname'] ?? explode(' ', $userData['name'])[1] ?? 'Elearning',
                    'email' => $userData['email'],
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
     * Request Single Sign-On One-Time Login URL from Moodle
     */
    public function getSsoLoginUrl(string $username): string
    {
        try {
            // Call Moodle auth_userkey_request_login_url or generate a secure key
            $response = $this->call('auth_userkey_request_login_url', [
                'user' => [
                    'username' => $username
                ]
            ]);

            if (isset($response['loginurl'])) {
                return $response['loginurl'];
            }

            // Fallback: If Moodle custom service returns nested array or direct key
            if (isset($response[0]['loginurl'])) {
                return $response[0]['loginurl'];
            }

            throw new Exception('loginurl not found in Moodle response.');
        } catch (Exception $e) {
            Log::error("Moodle SSO Failed for user {$username}: " . $e->getMessage());
            
            // Mock URL for testing if Moodle API is offline/unavailable in dev
            if (config('app.env') === 'local') {
                $mockToken = bin2hex(random_bytes(16));
                return rtrim($this->baseUrl, '/') . "/auth/userkey/login.php?key=" . $mockToken;
            }
            throw $e;
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
