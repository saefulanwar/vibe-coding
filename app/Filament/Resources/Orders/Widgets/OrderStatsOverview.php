<?php

namespace App\Filament\Resources\Orders\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;

class OrderStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $openOrders = Order::where('status', 'pending')->count();
        $averagePrice = Order::where('status', 'paid')->avg('amount') ?? 0;

        // Generate some dummy sparkline values for premium look
        $chartData = [12, 17, 14, 18, 22, 19, 24];

        return [
            Stat::make('Orders', $totalOrders)
                ->chart($chartData)
                ->color('success'),
            Stat::make('Open orders', $openOrders)
                ->color('warning'),
            Stat::make('Average price', 'Rp ' . number_format($averagePrice, 0, ',', '.'))
                ->color('info'),
        ];
    }
}
