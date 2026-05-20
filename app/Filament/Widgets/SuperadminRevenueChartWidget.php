<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Unit;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperadminRevenueChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pendapatan Per Unit'; // I need to change this
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '300px';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('super_admin');
    }

    protected function getData(): array
    {
        // Get total paid orders grouped by Unit
        // Order -> CourseBatch -> Course -> Unit
        $revenues = DB::table('units')
            ->select('units.name', DB::raw('SUM(orders.amount) as total_revenue'))
            ->leftJoin('courses', 'units.id', '=', 'courses.unit_id')
            ->leftJoin('course_batches', 'courses.id', '=', 'course_batches.course_id')
            ->leftJoin('orders', function ($join) {
                $join->on('course_batches.id', '=', 'orders.course_batch_id')
                     ->where('orders.status', '=', 'paid');
            })
            ->groupBy('units.id', 'units.name')
            ->havingRaw('SUM(orders.amount) > 0')
            ->get();

        $labels = $revenues->pluck('name')->toArray();
        $data = $revenues->pluck('total_revenue')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Pendapatan (Rp)',
                    'data' => $data,
                    'backgroundColor' => [
                        '#36A2EB',
                        '#FF6384',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
