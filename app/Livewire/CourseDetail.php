<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class CourseDetail extends Component
{
    public $course;
    public $batch;

    public function mount($slug)
    {
        $this->course = Course::where('slug', $slug)->with(['unit', 'category', 'modules.lessons'])->firstOrFail();
        
        // Find an active batch if possible, otherwise just use the latest one
        $this->batch = $this->course->batches()
            ->where(function($q) {
                $q->whereNull('registration_end_date')
                  ->orWhere('registration_end_date', '>=', now());
            })
            ->latest()
            ->first() 
            ?? $this->course->batches()->latest()->first();
    }

    public function render()
    {
        return view('livewire.course-detail')->title($this->course->title . ' - Glacier LMS');
    }
}
