<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;

class HeroSearch extends Component
{
    public $search = '';

    public function render()
    {
        $results = [];

        if (strlen($this->search) >= 2) {
            $results = Course::where('title', 'ilike', '%' . $this->search . '%')
                ->where('is_published', true)
                ->take(5)
                ->get();
        }

        return view('livewire.hero-search', [
            'results' => $results,
        ]);
    }
}
