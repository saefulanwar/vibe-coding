<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\Course;
use App\Models\Unit;

class CourseCatalog extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedUnit = null;
    public $priceFilter = 'all'; 
    public $deliveryFilter = 'all'; 
    public $sortBy = 'newest'; 

    #[On('filterByUnit')]
    public function setUnitFilter($unitId)
    {
        $this->selectedUnit = $unitId;
        $this->resetPage();
    }

    public function updatedSearch() { $this->resetPage(); }
    public function updatedSelectedUnit() { $this->resetPage(); }
    public function updatedPriceFilter() { $this->resetPage(); }
    public function updatedDeliveryFilter() { $this->resetPage(); }
    public function updatedSortBy() { $this->resetPage(); }

    #[Computed]
    public function units()
    {
        return Unit::orderBy('name', 'asc')->get();
    }

    #[Computed]
    public function courses()
    {
        // Poros utama diubah ke Course, batch ditampilkan di dalam masing-masing course
        $query = Course::query()
            ->where('is_published', true)
            ->with(['unit', 'category', 'batches' => function ($q) {
                $q->withCount('enrollments')
                  ->orderBy('created_at', 'desc');
            }]);

        // Filter: Kata Kunci Judul Kursus (Case-insensitive)
        if (!empty($this->search)) {
            $query->where('title', 'ilike', '%' . $this->search . '%');
        }

        // Filter: Unit / Fakultas
        if (!empty($this->selectedUnit)) {
            $query->where('unit_id', $this->selectedUnit);
        }

        // Filter: Harga Kursus
        if ($this->priceFilter === 'free') {
            $query->where('price', 0);
        } elseif ($this->priceFilter === 'paid') {
            $query->where('price', '>', 0);
        }

        // Filter: Metode Pembelajaran (Source)
        if ($this->deliveryFilter !== 'all') {
            $query->where('source', $this->deliveryFilter);
        }

        // Pengurutan (Sorting)
        if ($this->sortBy === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($this->sortBy === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($this->sortBy === 'price_desc') {
            $query->orderBy('price', 'desc');
        }

        // Mengembalikan data course dengan paging (9 course per halaman)
        return $query->paginate(9);
    }

    public function render()
    {
        return view('livewire.course-catalog');
    }
}