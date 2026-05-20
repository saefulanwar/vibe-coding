<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacultyAdminRevenueWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('admin_fakultas');
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $unitId = $user->unit_id; // Pastikan ini sesuai dengan nama kolom di DB Anda (unit_id / faculty_id)
        $unitName = $user->unit ? $user->unit->name : 'Unit Anda';

        $totalRevenue = 0;
        $totalCourses = 0;
        $totalOrders = 0;

        if ($unitId) {
            // 1. Hitung Total Pendapatan Unit
            $totalRevenue = DB::table('orders')
                ->join('course_batches', 'orders.course_batch_id', '=', 'course_batches.id')
                ->join('courses', 'course_batches.course_id', '=', 'courses.id')
                ->where('orders.status', 'paid')
                ->where('courses.unit_id', $unitId)
                ->sum('orders.amount');

            // 2. Hitung Total Kursus Aktif di Unit
            $totalCourses = Course::where('unit_id', $unitId)->count();

            // 3. Hitung Total Transaksi Berhasil (Tambahan untuk melengkapi Grid)
            $totalOrders = DB::table('orders')
                ->join('course_batches', 'orders.course_batch_id', '=', 'course_batches.id')
                ->join('courses', 'course_batches.course_id', '=', 'courses.id')
                ->where('orders.status', 'paid')
                ->where('courses.unit_id', $unitId)
                ->count();
        }

        return [
            // Kolom 1: Pendapatan
            Stat::make('Pendapatan ' . $unitName, 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total pendapatan dari kursus unit Anda')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            // Kolom 2: Total Kursus
            Stat::make('Total Kursus Unit', $totalCourses)
                ->description('Kursus yang dikelola oleh unit Anda')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),

            // Kolom 3: Total Transaksi (Membuat layout pas 3 kolom)
            Stat::make('Transaksi Sukses', $totalOrders . ' Order')
                ->description('Jumlah pembayaran berhasil dilakukan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),
        ];
    }
}