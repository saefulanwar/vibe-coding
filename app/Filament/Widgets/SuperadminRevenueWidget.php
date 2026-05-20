<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class SuperadminRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('super_admin');
    }

    protected function getStats(): array
    {
        $totalRevenue = Order::where('status', 'paid')->sum('amount');
        $totalUsers = User::count();
        $totalCourses = Course::where('is_published', true)->count();

        return [
            Stat::make('Total Pendapatan Global', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Seluruh pendapatan dari transaksi sukses')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Total Pengguna', $totalUsers)
                ->description('Jumlah pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Kursus Aktif', $totalCourses)
                ->description('Total kursus yang dipublikasikan')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
        ];
    }
}
