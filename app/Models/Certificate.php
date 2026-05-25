<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certificate extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    public function getFilePathAttribute($value)
    {
        $media = $this->getFirstMedia('certificates');
        if ($media) {
            return $media->id . '/' . $media->file_name;
        }
        return $value;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }
}
