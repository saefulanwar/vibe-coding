<?php

namespace App\Filament\Resources\Enrollments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;

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
                    ->icon(fn (string $state): ?string => match ($state) {
                        'moodle' => 'heroicon-m-arrow-top-right-on-square',
                        'local' => 'heroicon-m-home',
                        default => null,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'moodle' => 'warning',
                        'local' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'moodle' => 'LMS Moodle',
                        'local' => 'Lokal',
                        default => ucfirst($state),
                    })
                    ->toggleable(),
                TextColumn::make('enrolled_at')
                    ->label('Tanggal Terdaftar')
                    ->date('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Tanggal Kedaluwarsa')
                    ->date('d M Y, H:i')
                    ->placeholder('Lifetime Access')
                    ->sortable()
                    ->toggleable(),
            ])
            ->groups([
                Group::make('course.title')
                    ->label('Kursus'),
                Group::make('course.source')
                    ->label('Sumber'),
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
