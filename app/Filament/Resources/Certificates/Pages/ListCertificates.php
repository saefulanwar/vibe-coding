<?php

namespace App\Filament\Resources\Certificates\Pages;

use App\Filament\Resources\Certificates\CertificateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;

class ListCertificates extends ListRecords
{
    protected static string $resource = CertificateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Certificates\Widgets\CertificateStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'completed' => Tab::make('Selesai TTE')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'completed')),
            'processing' => Tab::make('Proses TTE')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'processing')),
            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending')),
            'failed' => Tab::make('Gagal')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'failed')),
        ];
    }
}
