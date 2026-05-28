<?php

namespace App\Filament\Resources\Courses\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Tables\Grouping\Group;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Kursus')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('idr')
                    ->sortable(),
                TextColumn::make('source')
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
                    ->sortable(),
                TextColumn::make('moodle_course_id')
                    ->label('Moodle ID')
                    ->placeholder('N/A')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_published')
                    ->label('Status Publik')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->date('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('category.name')
                    ->label('Kategori'),
                Group::make('source')
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
