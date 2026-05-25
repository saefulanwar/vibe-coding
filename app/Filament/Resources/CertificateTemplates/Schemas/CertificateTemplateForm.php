<?php

namespace App\Filament\Resources\CertificateTemplates\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CertificateTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make('Informasi Template')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('tag_koordinat')
                            ->label('Tag Koordinat TTE (SiAgen)')
                            ->required()
                            ->default('#')
                            ->helperText('Gunakan karakter spesifik (misal #) untuk deteksi posisi koordinat TTE oleh SiAgen.'),
                        \Filament\Forms\Components\ColorPicker::make('font_color')
                            ->label('Warna Teks Utama')
                            ->required()
                            ->default('#000000'),
                        SpatieMediaLibraryFileUpload::make('background_image')
                            ->label('Gambar Background')
                            ->collection('background_image')
                            ->disk('public')
                            ->image()
                            ->columnSpanFull()
                            ->helperText('Rekomendasi ukuran: 297x210 mm (A4 Landscape). Kosongkan area tengah untuk teks.'),
                    ]),
                \Filament\Schemas\Components\Section::make('Konten Khusus (Opsional)')
                    ->schema([
                        Textarea::make('content_html')
                            ->label('Struktur HTML Tambahan')
                            ->columnSpanFull()
                            ->helperText('Kosongkan jika ingin menggunakan struktur teks default. Hanya disarankan untuk pengguna tingkat lanjut.'),
                    ]),
            ]);
    }
}
