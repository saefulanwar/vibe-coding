<?php

namespace App\Filament\Resources\Enrollments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.title')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course.source')
                    ->label('Sumber')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'moodle' => 'warning',
                        'local' => 'primary',
                        default => 'gray',
                    }),
                TextColumn::make('courseBatch.course.unit.name')
                    ->label('Unit Kerja')
                    ->sortable()
                    ->placeholder('— Global —'),
                TextColumn::make('enrolled_at')
                    ->label('Tgl. Terdaftar')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Tgl. Kedaluwarsa')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Lifetime Access'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
