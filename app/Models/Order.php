<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_batch_id',
        'reference_number',
        'amount',
        'status',
        'payment_url',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
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
            'course_batch_id', // Local key on orders table...
            'course_id' // Local key on course_batches table...
        );
    }
}
