<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use App\Models\CourseBatch; // Poros utama diubah ke CourseBatch
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
    public function batches()
    {
        // Menggunakan join ke table courses agar filtering & sorting data induk menjadi efisien
        $query = CourseBatch::query()
            ->join('courses', 'course_batches.course_id', '=', 'courses.id')
            ->select('course_batches.*') // Mengamankan agar ID yang diambil tetap ID Batch, bukan ID Course
            ->where('courses.is_published', true)
            ->with(['course.unit', 'course.category']); // Eager loading relasi

        // Filter: Kata Kunci Judul Kursus (Case-insensitive)
        if (!empty($this->search)) {
            $query->where('courses.title', 'ilike', '%' . $this->search . '%');
        }

        // Filter: Unit / Fakultas
        if (!empty($this->selectedUnit)) {
            $query->where('courses.unit_id', $this->selectedUnit);
        }

        // Filter: Harga Kursus
        if ($this->priceFilter === 'free') {
            $query->where('courses.price', 0);
        } elseif ($this->priceFilter === 'paid') {
            $query->where('courses.price', '>', 0);
        }

        // Filter: Metode Pembelajaran (Source)
        if ($this->deliveryFilter !== 'all') {
            $query->where('courses.source', $this->deliveryFilter);
        }

        // Pengurutan (Sorting) Data Batch Publik
        if ($this->sortBy === 'newest') {
            $query->orderBy('course_batches.created_at', 'desc');
        } elseif ($this->sortBy === 'price_asc') {
            $query->orderBy('courses.price', 'asc');
        } elseif ($this->sortBy === 'price_desc') {
            $query->orderBy('courses.price', 'desc');
        }

        // Mengembalikan data batch dengan paging (9 batch per halaman)
        return $query->paginate(9);
    }

    public function render()
    {
        return view('livewire.course-catalog');
    }
}