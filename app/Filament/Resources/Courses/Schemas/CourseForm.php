<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dasar')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, callable $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(Category::pluck('name', 'id')->toArray())
                            ->nullable()
                            ->searchable(),
                        TextInput::make('price')
                            ->label('Harga (IDR)')
                            ->numeric()
                            ->required()
                            ->prefix('Rp')
                            ->helperText('Masukkan 0 jika kursus ini gratis (tidak berbayar).'),
                        SpatieMediaLibraryFileUpload::make('thumbnail')
                            ->label('Gambar Thumbnail')
                            ->collection('thumbnail')
                            ->disk('public')
                            ->image()
                            ->nullable(),
                        Toggle::make('is_published')
                            ->label('Publikasikan Kursus')
                            ->default(false),
                    ]),

                Section::make('Arsitektur Konten (Hybrid System)')
                    ->description('Pilih sumber materi pembelajaran untuk kursus ini.')
                    ->schema([
                        Select::make('source')
                            ->label('Sumber Konten')
                            ->options([
                                'local' => 'Konten Lokal (Laravel)',
                                'moodle' => 'Delegasi LMS Moodle',
                            ])
                            ->required()
                            ->live() // Memicu reaktivitas form secara instan!
                            ->default('local'),
                        
                        // Form Terpadu Reaktif: Hanya muncul jika source = moodle
                        Section::make('Sinkronisasi LMS Moodle')
                            ->visible(fn (callable $get) => $get('source') === 'moodle')
                            ->schema([
                                TextInput::make('moodle_course_id')
                                    ->label('Moodle Course ID')
                                    ->helperText('Masukkan ID kursus yang sesuai dari LMS Moodle Anda.')
                                    ->numeric()
                                    ->required(fn (callable $get) => $get('source') === 'moodle')
                                    ->unique(ignoreRecord: true),
                                Placeholder::make('moodle_notice')
                                    ->label('Catatan Sinkronisasi')
                                    ->content('Siswa yang melakukan pembelian kursus ini akan otomatis dibuatkan akun Moodle (jika belum ada) dan di-enroll langsung ke kelas terkait menggunakan Moodle API.'),
                            ])
                            ->columns(1),

                        // Form Terpadu Reaktif: Hanya muncul jika source = local
                        Section::make('Informasi Materi Lokal')
                            ->visible(fn (callable $get) => $get('source') === 'local')
                            ->schema([
                                Placeholder::make('local_notice')
                                    ->label('Catatan Materi Lokal')
                                    ->content('Materi pembelajaran dikelola secara lokal pada Laravel menggunakan struktur hirarki (Modules -> Lessons).'),
                            ]),
                    ]),

                Section::make('Deskripsi Kursus')
                    ->schema([
                        RichEditor::make('description')
                            ->label('Deskripsi Lengkap')
                            ->nullable(),
                    ]),

                Section::make('Sertifikat')
                    ->schema([
                        Select::make('certificate_template_id')
                            ->label('Template Sertifikat')
                            ->relationship('certificateTemplate', 'title')
                            ->nullable()
                            ->searchable(),
                        Toggle::make('requires_tte')
                            ->label('Gunakan Tanda Tangan Elektronik (TTE SiAgen)')
                            ->default(true),
                    ]),
            ]);
    }
}
