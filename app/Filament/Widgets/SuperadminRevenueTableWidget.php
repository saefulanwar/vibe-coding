<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\Orders\OrderResource;

class SuperadminRevenueTableWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('super_admin');
    }

    public function table(Table $table): Table
    {
        $query = Unit::query()
            ->select('units.id', 'units.name', \Illuminate\Support\Facades\DB::raw('COALESCE(SUM(orders.amount), 0) as total_revenue'))
            ->leftJoin('courses', 'units.id', '=', 'courses.unit_id')
            ->leftJoin('course_batches', 'courses.id', '=', 'course_batches.course_id')
            ->leftJoin('orders', function ($join) {
                $join->on('course_batches.id', '=', 'orders.course_batch_id')
                     ->where('orders.status', '=', 'paid');
            })
            ->groupBy('units.id', 'units.name');

        return $table
            ->query($query)
            ->heading('Rincian Pendapatan per Unit')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Unit Kerja')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Total Pendapatan')
                    ->money('idr')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Lihat Transaksi')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Unit $record): string => OrderResource::getUrl('index', [
                        'tableFilters' => [
                            'unit' => ['value' => $record->id],
                        ],
                    ])),
            ]);
    }
}
