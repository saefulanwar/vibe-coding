<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_batch_id', 'enrolled_at', 'expires_at'];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courseBatch()
    {
        return $this->belongsTo(CourseBatch::class);
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            CourseBatch::class,
            'id', // Foreign key on course_batches table...
            'id', // Foreign key on courses table...
            'course_batch_id', // Local key on enrollments table...
            'course_id' // Local key on course_batches table...
        );
    }
}
