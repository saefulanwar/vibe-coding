<?php

namespace App\Filament\Resources\Enrollments\Pages;

use App\Filament\Resources\Enrollments\EnrollmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;

class ListEnrollments extends ListRecords
{
    protected static string $resource = EnrollmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Enrollments\Widgets\EnrollmentStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'local' => Tab::make('Aplikasi Glacier (Lokal)')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('course', fn ($q) => $q->where('source', 'local'))),
            'moodle' => Tab::make('LMS Moodle')
                ->modifyQueryUsing(fn ($query) => $query->whereHas('course', fn ($q) => $q->where('source', 'moodle'))),
        ];
    }
}
