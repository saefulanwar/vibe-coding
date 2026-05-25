<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

#[Fillable(['name', 'email', 'password', 'role', 'moodle_user_id', 'provider_name', 'provider_id', 'unit_id', 'nik', 'nim', 'phone_number', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('super_admin');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function enrolledBatches()
    {
        return $this->belongsToMany(CourseBatch::class, 'enrollments', 'user_id', 'course_batch_id')
            ->withPivot('enrolled_at', 'expires_at')
            ->withTimestamps();
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'id')
            ->join('course_batches', 'enrollments.course_batch_id', '=', 'course_batches.id')
            ->join('courses', 'course_batches.course_id', '=', 'courses.id')
            ->select('courses.*')
            ->withTimestamps();
    }

    /**
     * Blocked placeholder names from Moodle auto-generation or generic registrations.
     */
    protected static array $blockedNames = [
        'student', 'elearning', 'user', 'admin', 'test', 'default',
        'pengguna', 'siswa', 'mahasiswa', 'peserta',
    ];

    /**
     * Check if the user's email belongs to a UNY student domain.
     */
    public function isStudentEmail(): bool
    {
        return str_ends_with(strtolower($this->email), '@student.uny.ac.id');
    }

    /**
     * Determine if the user profile is complete for course transactions.
     * Acts as a data cleansing gate for certificate generation integrity.
     */
    public function isProfileComplete(): bool
    {
        // 1. Name must not be empty or a blocked placeholder
        if (empty($this->name) || in_array(strtolower(trim($this->name)), static::$blockedNames)) {
            return false;
        }

        // 2. NIK must be exactly 16 digits
        if (empty($this->nik) || !preg_match('/^\d{16}$/', $this->nik)) {
            return false;
        }

        // 3. Phone number must be present and valid Indonesian format
        if (empty($this->phone_number)) {
            return false;
        }

        // 4. NIM is required only for @student.uny.ac.id emails
        if ($this->isStudentEmail() && empty($this->nim)) {
            return false;
        }

        return true;
    }

    /**
     * Get a human-readable list of missing profile fields.
     */
    public function getMissingProfileFields(): array
    {
        $missing = [];

        if (empty($this->name) || in_array(strtolower(trim($this->name)), static::$blockedNames)) {
            $missing[] = 'Nama Lengkap';
        }
        if (empty($this->nik) || !preg_match('/^\d{16}$/', $this->nik)) {
            $missing[] = 'NIK';
        }
        if (empty($this->phone_number)) {
            $missing[] = 'No. HP';
        }
        if ($this->isStudentEmail() && empty($this->nim)) {
            $missing[] = 'NIM';
        }

        return $missing;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }
}
