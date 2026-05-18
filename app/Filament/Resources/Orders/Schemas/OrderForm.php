<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\User;
use App\Models\Course;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Transaksi')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference_number')
                            ->label('Nomor Referensi')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('user_id')
                            ->label('Siswa')
                            ->options(User::pluck('name', 'id')->toArray())
                            ->required()
                            ->searchable(),
                        Select::make('course_id')
                            ->label('Kursus')
                            ->options(Course::pluck('title', 'id')->toArray())
                            ->required()
                            ->searchable(),
                        TextInput::make('amount')
                            ->label('Jumlah (IDR)')
                            ->numeric()
                            ->required()
                            ->prefix('Rp'),
                        Select::make('status')
                            ->label('Status Transaksi')
                            ->options([
                                'pending' => 'Pending',
                                'paid' => 'Lunas (Paid)',
                                'failed' => 'Gagal (Failed)',
                                'expired' => 'Kedaluwarsa (Expired)',
                            ])
                            ->required()
                            ->default('pending'),
                        TextInput::make('payment_url')
                            ->label('URL Pembayaran Gateway')
                            ->nullable()
                            ->url(),
                    ]),

                Section::make('Respon Webhook Gateway (JSONB)')
                    ->schema([
                        KeyValue::make('gateway_response')
                            ->label('Log Respon JSON')
                            ->keyLabel('Kunci (Key)')
                            ->valueLabel('Nilai (Value)')
                            ->nullable(),
                    ])
                    ->collapsible(),
            ]);
    }
}
