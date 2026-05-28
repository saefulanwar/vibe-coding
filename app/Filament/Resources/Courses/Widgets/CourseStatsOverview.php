<?php

namespace App\Filament\Resources\Courses\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Course;

class CourseStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalCourses = Course::count();
        $publishedCourses = Course::where('is_published', true)->count();
        $localCourses = Course::where('source', 'local')->count();

        // Premium sparkline chart data
        $chartData = [5, 8, 12, 10, 15, 14, 18];

        return [
            Stat::make('Total Kursus', $totalCourses)
                ->chart($chartData)
                ->color('success'),
            Stat::make('Terbit (Dipublikasikan)', $publishedCourses)
                ->color('primary'),
            Stat::make('Kursus Lokal', $localCourses)
                ->color('info'),
        ];
    }
}
