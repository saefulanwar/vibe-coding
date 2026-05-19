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
        
        $postData = array_merge([
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => 'json',
        ], $params);

        try {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Expect:',
                'Accept-Encoding: identity',
            ]);

            $result = curl_exec($ch);
            
            if ($result === false) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new Exception("cURL Error: " . $error);
            }
            
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 400) {
                throw new Exception("HTTP request failed with status: " . $httpCode);
            }

            $data = json_decode($result, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("JSON Decode Error: " . json_last_error_msg() . " | Raw: " . substr($result, 0, 100));
            }

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
     * Get Moodle User by Email
     */
    public function getMoodleUserByEmail(string $email): ?int
    {
        $params = [
            'criteria' => [
                [
                    'key' => 'email',
                    'value' => $email,
                ]
            ]
        ];

        try {
            // Usually core_user_get_users returns something like { "users": [ { "id": 123, ... } ] }
            // But our call wrapper might return it directly or wrapped in an array.
            $response = $this->call('core_user_get_users', $params);
            
            // If response is nested in 'users' key
            if (isset($response['users']) && is_array($response['users']) && count($response['users']) > 0) {
                return (int) $response['users'][0]['id'];
            }
            
            // If response is the array of users directly
            if (is_array($response) && isset($response[0]['id'])) {
                return (int) $response[0]['id'];
            }
        } catch (Exception $e) {
            Log::warning("Moodle User Get Failed: " . $e->getMessage());
        }

        return null;
    }

    public function createMoodleUser(array $userData): int
    {
        $nim = explode('@', $userData['email'])[0];
        $username = strtolower($nim);

        $params = [
            'users' => [
                [
                    'username' => $username,
                    'password' => $userData['password'] ?? 'P@ssw0rd123!',
                    'firstname' => $userData['name'] ?? 'Peserta Elearning',
                    'lastname' => $userData['lastname'] ?? $nim,
                    'email' => $userData['email'],
                    'auth' => 'oauth2',
                ]
            ]
        ];

        try {
            $response = $this->call('core_user_create_users', $params);

            if (!empty($response) && isset($response[0]['id'])) {
                return (int) $response[0]['id'];
            }
            
            // Fallback: Get user by email if creation was successful but ID isn't clearly returned
            $moodleId = $this->getMoodleUserByEmail($userData['email']);

            if ($moodleId) {
                return $moodleId;
            }

            throw new Exception("Moodle API Exception: Could not determine user ID from response.");
        } catch (Exception $e) {
            Log::error("Moodle Create User Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function enrollUserInCourse(int $moodleUserId, int $moodleCourseId, int $roleId = 5): bool
    {
        $params = [
            'enrolments' => [
                [
                    'roleid' => $roleId, // 5 = Student
                    'userid' => $moodleUserId,
                    'courseid' => $moodleCourseId,
                    'timestart' => 0,
                    'timeend' => 0,
                    'suspend' => 0,
                ]
            ]
        ];

        try {
            $this->call('enrol_manual_enrol_users', $params);
            return true;
        } catch (Exception $e) {
            Log::error("Moodle Enroll Error: " . $e->getMessage());
            throw $e;
        }
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
            Log::error("Moodle Create Group Error: " . $e->getMessage());
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
            Log::error("Moodle Add User To Group Error: " . $e->getMessage());
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
