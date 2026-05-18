<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use App\Models\User;
use App\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akses Belajar Siswa')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->label('Siswa')
                            ->options(User::pluck('name', 'id')->toArray())
                            ->required()
                            ->searchable(),
                        Select::make('course_id')
                            ->label('Kursus')
                            ->options(Course::pluck('title', 'id')->toArray())
                            ->required()
                            ->searchable(),
                        DateTimePicker::make('enrolled_at')
                            ->label('Tanggal Terdaftar')
                            ->required()
                            ->default(now()),
                        DateTimePicker::make('expires_at')
                            ->label('Tanggal Kedaluwarsa')
                            ->helperText('Kosongkan untuk akses seumur hidup (lifetime access).')
                            ->nullable(),
                    ]),
            ]);
    }
}
