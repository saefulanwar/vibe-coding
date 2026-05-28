<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'review_text',
        'status',
        'reply_text',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected static function booted()
    {
        static::saved(function ($review) {
            if ($review->course) {
                $review->course->recalculateRating();
            }
        });

        static::deleted(function ($review) {
            if ($review->course) {
                $review->course->recalculateRating();
            }
        });

        static::restored(function ($review) {
            if ($review->course) {
                $review->course->recalculateRating();
            }
        });
    }
}
