<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Unit;

class FacultyGrid extends Component
{
    public function render()
    {
        $units = Unit::withCount('courses')->get();
        
        return view('livewire.faculty-grid', [
            'units' => $units
        ]);
    }
}
