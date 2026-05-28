<?php

namespace App\Filament\Resources\CourseReviews\Pages;

use App\Filament\Resources\CourseReviews\CourseReviewResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListCourseReviews extends ListRecords
{
    protected static string $resource = CourseReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Ulasan'),
            'published' => Tab::make('Dipublikasikan')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'published')),
            'hidden' => Tab::make('Disembunyikan')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'hidden')),
            'unreplied' => Tab::make('Belum Dibalas')
                ->modifyQueryUsing(fn ($query) => $query->whereNull('reply_text')->orWhere('reply_text', '')),
        ];
    }
}
