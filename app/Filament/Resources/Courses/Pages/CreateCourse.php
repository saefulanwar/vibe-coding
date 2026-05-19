<?php

namespace App\Filament\Resources\Courses\Pages;

use App\Filament\Resources\Courses\CourseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    /**
     * Mutate form data before creating.
     * Ensures unit_id is always set for unit-scoped users.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && $user->unit_id) {
            $data['unit_id'] = $user->unit_id;
        }

        return $data;
    }
}
