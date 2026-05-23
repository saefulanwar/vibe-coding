<?php

namespace App\Livewire;

use Closure;
use Filament\Forms\Components\TextInput;
use Illuminate\Contracts\Validation\ValidationRule;
use Jeffgreco13\FilamentBreezy\Livewire\PersonalInfo;

class MyPersonalInfo extends PersonalInfo
{
    /**
     * Fields managed by this component – Breezy only saves columns listed here.
     */
    public array $only = ['name', 'nik', 'nim', 'phone_number'];

    /**
     * Define the profile form schema with strict validation rules.
     */
    protected function getProfileFormSchema(): array
    {
        $user = auth()->user();

        $schema = [
            $this->getNameComponent()
                ->required()
                ->rules([
                    'required',
                    'string',
                    'max:255',
                    new class implements ValidationRule {
                        public function validate(string $attribute, mixed $value, Closure $fail): void
                        {
                            $blocked = ['student', 'elearning', 'user', 'admin', 'test', 'default', 'pengguna', 'siswa', 'mahasiswa', 'peserta'];
                            if (in_array(strtolower(trim($value)), $blocked)) {
                                $fail('Nama "' . $value . '" tidak diperbolehkan. Gunakan nama lengkap asli Anda.');
                            }
                        }
                    },
                ])
                ->helperText('Wajib menggunakan nama asli sesuai dokumen identitas.')
                ->placeholder('Contoh: Budi Santoso'),

            TextInput::make('nik')
                ->label('Nomor Induk Kependudukan (NIK)')
                ->required()
                ->string()
                ->rules(['required', 'regex:/^\d{16}$/'])
                ->maxLength(16)
                ->placeholder('Contoh: 3404123456789012')
                ->helperText('Wajib 16 digit angka. Diperlukan untuk penerbitan sertifikat ber-TTE.'),

            TextInput::make('phone_number')
                ->label('No. HP / WhatsApp')
                ->required()
                ->string()
                ->rules(['required', 'regex:/^(\+62|08)\d{7,12}$/'])
                ->maxLength(20)
                ->placeholder('Contoh: 081234567890')
                ->helperText('Format Indonesia: dimulai dengan 08 atau +62.'),
        ];

        // NIM field: only visible for @student.uny.ac.id emails
        if ($user && str_ends_with(strtolower($user->email), '@student.uny.ac.id')) {
            $schema[] = TextInput::make('nim')
                ->label('Nomor Induk Mahasiswa (NIM)')
                ->required()
                ->string()
                ->rules(['required', 'regex:/^\d{1,14}$/'])
                ->maxLength(14)
                ->placeholder('Contoh: 21501244001')
                ->helperText('Wajib diisi untuk mahasiswa UNY (maks. 14 digit).');
        }

        return $schema;
    }

    /**
     * Override submit to handle auto-redirect back to intended URL.
     */
    public function submit(): void
    {
        parent::submit();

        $user = auth()->user();

        if (session()->has('url.intended') && $user && $user->isProfileComplete()) {
            $intendedUrl = session()->pull('url.intended');
            $this->redirect($intendedUrl);
        }
    }
}
