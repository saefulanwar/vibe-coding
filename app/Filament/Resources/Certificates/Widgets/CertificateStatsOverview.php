<?php

namespace App\Filament\Resources\Certificates\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Certificate;

class CertificateStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCertificates = Certificate::count();
        $completedCertificates = Certificate::where('status', 'completed')->count();
        $failedCertificates = Certificate::where('status', 'failed')->count();

        // Premium sparkline chart data
        $chartData = [8, 12, 10, 16, 22, 19, 25];

        return [
            Stat::make('Total Sertifikat', $totalCertificates)
                ->chart($chartData)
                ->color('success'),
            Stat::make('Selesai TTE', $completedCertificates)
                ->color('primary'),
            Stat::make('Gagal TTE', $failedCertificates)
                ->color('danger'),
        ];
    }
}
