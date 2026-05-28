<?php

namespace App\Filament\Resources\CourseReviews\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CourseReviewForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ulasan Pengguna')
                    ->columns(2)
                    ->schema([
                        Placeholder::make('user_name')
                            ->label('Nama Siswa')
                            ->content(fn ($record) => $record?->user?->name ?? '-'),
                        Placeholder::make('course_title')
                            ->label('Kursus')
                            ->content(fn ($record) => $record?->course?->title ?? '-'),
                        Placeholder::make('rating_display')
                            ->label('Rating Bintang')
                            ->content(fn ($record) => str_repeat('⭐', $record?->rating ?? 0) . ' (' . ($record?->rating ?? 0) . '/5)'),
                        Placeholder::make('created_at_display')
                            ->label('Dibuat Pada')
                            ->content(fn ($record) => $record?->created_at?->format('d M Y, H:i') ?? '-'),
                        Placeholder::make('review_text_display')
                            ->label('Ulasan Teks')
                            ->content(fn ($record) => $record?->review_text ?? '(Hanya rating bintang)')
                            ->columnSpan(2),
                    ]),

                Section::make('Moderasi & Tanggapan Resmi')
                    ->description('Kelola publikasi ulasan ini dan berikan tanggapan resmi dari pihak pengajar/admin.')
                    ->columns(1)
                    ->schema([
                        Select::make('status')
                            ->label('Status Publikasi')
                            ->options([
                                'published' => 'Dipublikasikan (Tampil di Katalog)',
                                'hidden' => 'Disembunyikan (Unpublished/Spam)',
                            ])
                            ->required()
                            ->default('published'),
                        
                        Textarea::make('reply_text')
                            ->label('Tanggapan Resmi (Reply)')
                            ->placeholder('Berikan tanggapan resmi pengajar/admin terhadap ulasan dari siswa ini...')
                            ->rows(4)
                            ->nullable(),
                    ]),
            ]);
    }
}
