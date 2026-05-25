<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CertificateTemplate extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    public function getKey()
    {
        $key = parent::getKey();
        return is_null($key) ? null : (string) $key;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function getBackgroundImageAttribute($value)
    {
        return $this->getFirstMediaUrl('background_image') ?: $value;
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }
}
