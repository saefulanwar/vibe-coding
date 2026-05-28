<?php

namespace App\Filament\Resources\CourseReviews;

use App\Filament\Resources\CourseReviews\Pages\EditCourseReview;
use App\Filament\Resources\CourseReviews\Pages\ListCourseReviews;
use App\Filament\Resources\CourseReviews\Schemas\CourseReviewForm;
use App\Filament\Resources\CourseReviews\Tables\CourseReviewsTable;
use App\Models\CourseReview;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseReviewResource extends Resource
{
    protected static ?string $model = CourseReview::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static string|\UnitEnum|null $navigationGroup = 'Kursus';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Ulasan Kursus';

    public static function form(Schema $schema): Schema
    {
        return CourseReviewForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseReviewsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseReviews::route('/'),
            'edit' => EditCourseReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user && $user->hasRole('admin_fakultas')) {
            $query->whereHas('course', function ($q) use ($user) {
                $q->where('unit_id', $user->unit_id);
            });
        }
        return $query;
    }
}
