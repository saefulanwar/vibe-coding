<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    public function getThumbnailAttribute($value)
    {
        return $this->getFirstMediaUrl('thumbnail') ?: $value;
    }

    public function getKey()
    {
        $key = parent::getKey();
        return is_null($key) ? null : (string) $key;
    }

    public function getKeyType()
    {
        return 'string';
    }

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'thumbnail',
        'description',
        'price',
        'is_published',
        'source',
        'moodle_course_id',
        'unit_id',
        'certificate_template_id',
        'requires_tte',
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

    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    public function recalculateRating()
    {
        $publishedReviews = $this->reviews()->where('status', 'published')->get();
        
        $this->reviews_count = $publishedReviews->count();
        $this->average_rating = $this->reviews_count > 0 ? $publishedReviews->avg('rating') : 0.00;
        
        $this->saveQuietly();
    }
}
