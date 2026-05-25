<?php

namespace App\Filament\Resources\Certificates\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CertificateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('course_id')
                    ->required()
                    ->numeric(),
                TextInput::make('unit_id')
                    ->numeric(),
                TextInput::make('template_id')
                    ->numeric(),
                TextInput::make('student_name_snapshot')
                    ->required(),
                TextInput::make('course_title_snapshot')
                    ->required(),
                DateTimePicker::make('completed_at'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('file_path'),
            ]);
    }
}
