<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';

    protected static ?string $title = 'Kurikulum & Silabus (Modul & Lessons)';

    protected static ?string $modelLabel = 'Modul';

    protected static ?string $pluralModelLabel = 'Modul';

    public static function canViewForRecord(Model $activeRecord, string $pageClass): bool
    {
        return $activeRecord->source === 'local';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Modul')
                    ->required()
                    ->columnSpan(2),
                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->required()
                    ->columnSpan(1),
                Repeater::make('lessons')
                    ->relationship('lessons')
                    ->label('Daftar Pelajaran (Lessons)')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Pelajaran')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('video_url')
                            ->label('URL Video')
                            ->url()
                            ->nullable()
                            ->columnSpan(2),
                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->columnSpan(1),
                        RichEditor::make('content_text')
                            ->label('Materi Teks')
                            ->nullable()
                            ->columnSpanFull(),
                    ])
                    ->columns(5)
                    ->columnSpanFull()
                    ->defaultItems(0)
                    ->reorderable('sort_order')
                    ->collapsible()
                    ->collapsed(true)
                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Judul Modul')
                    ->searchable(),
                TextColumn::make('lessons_count')
                    ->label('Jumlah Pelajaran')
                    ->state(fn ($record) => $record->lessons()->count() . ' Pelajaran'),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
