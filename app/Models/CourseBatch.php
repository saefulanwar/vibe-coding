<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\MoodleService;
use Illuminate\Support\Facades\Log;

class CourseBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'moodle_group_id',
        'quota',
        'start_date',
        'end_date',
        'registration_end_date',
    ];

    protected $casts = [
        'moodle_group_id' => 'integer',
        'quota' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_end_date' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (CourseBatch $batch) {
            if (empty($batch->moodle_group_id)) {
                $course = $batch->course;
                if ($course && $course->source === 'moodle' && !empty($course->moodle_course_id)) {
                    $moodleService = app(MoodleService::class);
                    try {
                        $groupId = $moodleService->createMoodleGroup($course->moodle_course_id, $batch->name);
                        $batch->moodle_group_id = $groupId;
                    } catch (\Exception $e) {
                        Log::error('Failed to auto-create Moodle group for batch ' . $batch->name . ': ' . $e->getMessage());
                        if (config('app.env') !== 'local') {
                            throw $e;
                        }
                    }
                }
            }
        });
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
