<?php

namespace App\Filament\Resources\CourseReviews\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use App\Models\Course;

class CourseReviewsTable
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
                
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . ' (' . $state . ')')
                    ->sortable(),
                
                TextColumn::make('review_text')
                    ->label('Cuplikan Ulasan')
                    ->limit(50)
                    ->searchable()
                    ->placeholder('(Hanya rating)'),
                
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->icon(fn (string $state): ?string => match ($state) {
                        'published' => 'heroicon-m-check-circle',
                        'hidden' => 'heroicon-m-eye-slash',
                        default => null,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'hidden' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Dipublikasikan',
                        'hidden' => 'Disembunyikan',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                
                TextColumn::make('reply_text')
                    ->label('Status Balasan')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? 'Sudah Dibalas' : 'Belum Dibalas')
                    ->placeholder('Belum Dibalas'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y, H:i')
                    ->sortable(),
            ])
            ->groups([
                Group::make('course.title')
                    ->label('Kursus'),
                Group::make('rating')
                    ->label('Rating Bintang'),
                Group::make('status')
                    ->label('Status Publikasi'),
            ])
            ->filters([
                SelectFilter::make('rating')
                    ->label('Rating Bintang')
                    ->options([
                        '1' => '⭐ (1)',
                        '2' => '⭐⭐ (2)',
                        '3' => '⭐⭐⭐ (3)',
                        '4' => '⭐⭐⭐⭐ (4)',
                        '5' => '⭐⭐⭐⭐⭐ (5)',
                    ]),
                SelectFilter::make('course_id')
                    ->label('Kursus')
                    ->options(fn () => Course::pluck('title', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'published' => 'Dipublikasikan',
                        'hidden' => 'Disembunyikan',
                    ]),
            ])
            ->actions([
                EditAction::make()
                    ->label('Balas / Edit'),
                
                Action::make('toggle_status')
                    ->label(fn ($record) => $record->status === 'published' ? 'Sembunyikan' : 'Tampilkan')
                    ->icon(fn ($record) => $record->status === 'published' ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn ($record) => $record->status === 'published' ? 'danger' : 'success')
                    ->action(function ($record) {
                        $newStatus = $record->status === 'published' ? 'hidden' : 'published';
                        $record->update(['status' => $newStatus]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Status Ulasan Diperbarui')
                            ->body('Status ulasan siswa berhasil diubah menjadi: ' . ($newStatus === 'published' ? 'Dipublikasikan' : 'Disembunyikan'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
