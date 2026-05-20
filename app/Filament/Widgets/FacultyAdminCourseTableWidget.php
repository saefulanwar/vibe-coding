<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FacultyAdminCourseTableWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('admin_fakultas');
    }

    public function table(Table $table): Table
    {
        $unitId = Auth::user()->unit_id;

        $query = Course::query()
            ->select(
                'courses.id',
                'courses.title',
                'courses.price',
                'courses.is_published',
                DB::raw('COALESCE(COUNT(DISTINCT orders.id), 0) as total_sold'),
                DB::raw('COALESCE(SUM(orders.amount), 0) as total_revenue')
            )
            ->leftJoin('course_batches', 'courses.id', '=', 'course_batches.course_id')
            ->leftJoin('orders', function ($join) {
                $join->on('course_batches.id', '=', 'orders.course_batch_id')
                     ->where('orders.status', '=', 'paid');
            })
            ->where('courses.unit_id', $unitId)
            ->groupBy('courses.id', 'courses.title', 'courses.price', 'courses.is_published');

        return $table
            ->query($query)
            ->heading('Kursus Terjual - Unit Anda')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Kursus')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sold')
                    ->label('Jumlah Terjual')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('idr')
                    ->sortable(),
            ]);
    }
}
