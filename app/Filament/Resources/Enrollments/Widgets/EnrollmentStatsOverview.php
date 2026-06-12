<?php

namespace App\Filament\Resources\Enrollments\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Enrollment;

class EnrollmentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalEnrollments = Enrollment::count();
        
        $localEnrollments = Enrollment::whereHas('course', function ($query) {
            $query->where('source', 'local');
        })->count();

        $moodleEnrollments = Enrollment::whereHas('course', function ($query) {
            $query->where('source', 'moodle');
        })->count();

        // Premium sparkline chart data
        $chartData = [15, 20, 18, 25, 30, 28, 35];

        return [
            Stat::make('Total Pendaftaran', $totalEnrollments)
                ->chart($chartData)
                ->color('success'),
            Stat::make('Aplikasi Glacier (Lokal)', $localEnrollments)
                ->color('primary'),
            Stat::make('LMS Moodle', $moodleEnrollments)
                ->color('warning'),
        ];
    }
}
