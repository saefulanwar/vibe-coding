<?php

namespace App\Filament\Resources\Certificates\Tables;

use App\Jobs\ProcessCertificateTteJob;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Grouping\Group;

class CertificatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student_name_snapshot')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('course_title_snapshot')
                    ->label('Kursus')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->icon(fn (string $state): ?string => match ($state) {
                        'completed' => 'heroicon-m-check-circle',
                        'processing' => 'heroicon-m-arrow-path',
                        'pending' => 'heroicon-m-clock',
                        'failed' => 'heroicon-m-x-circle',
                        default => null,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'warning',
                        'pending' => 'gray',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai TTE',
                        'processing' => 'Proses TTE',
                        'pending' => 'Pending',
                        'failed' => 'Gagal TTE',
                        default => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('completed_at')
                    ->label('Lulus Pada')
                    ->date('d M Y, H:i')
                    ->placeholder('Belum Lulus')
                    ->sortable(),
                TextColumn::make('file_path')
                    ->label('Link Sertifikat')
                    ->formatStateUsing(fn ($state) => $state ? 'Unduh PDF' : '-')
                    ->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                    ->openUrlInNewTab()
                    ->color(fn ($state) => $state ? 'primary' : 'gray')
                    ->icon(fn ($state) => $state ? 'heroicon-o-document-arrow-down' : null)
                    ->toggleable(),
            ])
            ->groups([
                Group::make('status')
                    ->label('Status TTE'),
                Group::make('course_title_snapshot')
                    ->label('Kursus'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                \Filament\Actions\Action::make('sign_siagen')
                    ->label('Tandatangan SiAgen')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->url(fn ($record) => $record->siagen_manual_url)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->siagen_manual_url)),
                \Filament\Actions\Action::make('sync_tte')
                    ->label('Sinkronisasi TTE')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn ($record) => !empty($record->siagen_gateway_url))
                    ->action(function ($record) {
                        try {
                            $downloadResponse = \Illuminate\Support\Facades\Http::timeout(60)->get($record->siagen_gateway_url);
                            if ($downloadResponse->successful() && str_starts_with($downloadResponse->body(), '%PDF')) {
                                $finalPdfPath = 'certificates/' . $record->id . '.pdf';
                                $absolutePath = storage_path('app/public/' . $finalPdfPath);
                                
                                \Illuminate\Support\Facades\Storage::disk('public')->put($finalPdfPath, $downloadResponse->body());

                                $record->clearMediaCollection('certificates');
                                $record->addMedia($absolutePath)->toMediaCollection('certificates');

                                $record->update([
                                    'status' => 'completed',
                                    'file_path' => $finalPdfPath,
                                ]);

                                \Filament\Notifications\Notification::make()
                                    ->title('Sinkronisasi TTE Berhasil')
                                    ->body('Sertifikat bertanda tangan digital berhasil diunduh dari SiAgen.')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('Sinkronisasi TTE Gagal')
                                    ->body('Dokumen di SiAgen belum ditandatangani secara digital atau link unduhan tidak valid.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Error Sinkronisasi')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
