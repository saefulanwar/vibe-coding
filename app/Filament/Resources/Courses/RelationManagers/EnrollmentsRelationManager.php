<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Jobs\ProcessCertificateTteJob;
use App\Models\Certificate;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class EnrollmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'enrollments';

    protected static ?string $recordTitleAttribute = 'user.name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('user.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Peserta'),
                Tables\Columns\TextColumn::make('enrolled_at')
                    ->label('Tanggal Daftar')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('generateCertificate')
                        ->label('Generate Sertifikat')
                        ->icon('heroicon-o-document-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->form(function () {
                            $course = $this->getOwnerRecord();
                            if ($course->requires_tte) {
                                return [
                                    TextInput::make('nik')
                                        ->label('NIK Admin')
                                        ->required(),
                                    TextInput::make('passphrase')
                                        ->label('Passphrase TTE')
                                        ->password()
                                        ->required(),
                                ];
                            }
                            return [];
                        })
                        ->action(function (Collection $records, array $data) {
                            $course = $this->getOwnerRecord();
                            $template = $course->certificateTemplate;

                            if (!$template) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Error')
                                    ->body('Template sertifikat belum diatur untuk kursus ini.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $encryptedPassphrase = $course->requires_tte ? Crypt::encryptString($data['passphrase']) : null;

                            foreach ($records as $enrollment) {
                                $user = $enrollment->user;

                                // Check if certificate already exists
                                $existingCert = Certificate::where('user_id', $user->id)
                                    ->where('course_id', $course->id)
                                    ->first();

                                if ($existingCert) {
                                    continue;
                                }

                                $certificate = Certificate::create([
                                    'user_id' => $user->id,
                                    'course_id' => $course->id,
                                    'unit_id' => $course->unit_id,
                                    'template_id' => $template->id,
                                    'student_name_snapshot' => $user->name,
                                    'course_title_snapshot' => $course->title,
                                    'status' => 'pending',
                                    'completed_at' => now(),
                                ]);

                                if ($course->requires_tte) {
                                    ProcessCertificateTteJob::dispatch($certificate, $data['nik'], $encryptedPassphrase);
                                } else {
                                    // Process without TTE
                                    \App\Jobs\ProcessCertificateJob::dispatch($certificate);
                                }
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Proses Generate Sertifikat Dimulai')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion()
                ]),
            ]);
    }
}
