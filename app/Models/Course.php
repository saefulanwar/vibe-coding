<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'unit_id',
        'title',
        'slug',
        'thumbnail',
        'description',
        'price',
        'is_published',
        'source',
        'moodle_course_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('sort_order');
    }

    public function batches()
    {
        return $this->hasMany(CourseBatch::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, CourseBatch::class);
    }

    public function enrollments()
    {
        return $this->hasManyThrough(Enrollment::class, CourseBatch::class);
    }

    public function enrolledUsers()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_batch_id', 'user_id')
            ->whereIn('course_batch_id', $this->batches()->select('id'))
            ->withPivot('enrolled_at', 'expires_at')
            ->withTimestamps();
    }
}
