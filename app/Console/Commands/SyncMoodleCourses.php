<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\MoodleService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SyncMoodleCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'moodle:sync-courses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync courses catalog from Moodle LMS';

    protected MoodleService $moodleService;

    public function __construct(MoodleService $moodleService)
    {
        parent::__construct();
        $this->moodleService = $moodleService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Moodle courses catalog synchronization...');
        Log::info('Moodle catalog sync command started.');

        try {
            $moodleCourses = $this->moodleService->getMoodleCourses();

            if (empty($moodleCourses)) {
                $this->warn('No courses retrieved from Moodle or Moodle is not configured.');
                return Command::SUCCESS;
            }

            $syncedCount = 0;

            foreach ($moodleCourses as $mCourse) {
                // Ignore Moodle default site course (usually ID 1)
                if (isset($mCourse['id']) && $mCourse['id'] == 1) {
                    continue;
                }

                $courseId = $mCourse['id'] ?? null;
                $fullname = $mCourse['fullname'] ?? null;
                $summary = $mCourse['summary'] ?? null;

                if (!$courseId || !$fullname) {
                    continue;
                }

                // Clean summary HTML if any
                $description = strip_tags($summary);

                // Sync Course
                $course = Course::updateOrCreate(
                    [
                        'moodle_course_id' => $courseId,
                    ],
                    [
                        'title' => $fullname,
                        'slug' => Str::slug($fullname) . '-' . $courseId,
                        'description' => $description ?: 'Kursus didelegasikan ke LMS Moodle.',
                        'price' => 0.00, // Free by default, admin can change price in Filament
                        'source' => 'moodle',
                        'is_published' => true,
                    ]
                );

                $this->line("Synced: {$fullname} (Moodle Course ID: {$courseId})");
                $syncedCount++;
            }

            $this->info("Catalog synchronization complete. Synced {$syncedCount} courses.");
            Log::info("Moodle catalog sync successfully completed. Synced {$syncedCount} courses.");
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to sync Moodle courses: ' . $e->getMessage());
            Log::error('Moodle sync catalog error: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
