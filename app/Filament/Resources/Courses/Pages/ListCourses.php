<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;

class ListCourses extends ListRecords
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\Courses\Widgets\CourseStatsOverview::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua'),
            'published' => Tab::make('Dipublikasikan')
                ->modifyQueryUsing(fn ($query) => $query->where('is_published', true)),
            'draft' => Tab::make('Draf')
                ->modifyQueryUsing(fn ($query) => $query->where('is_published', false)),
        ];
    }
}
