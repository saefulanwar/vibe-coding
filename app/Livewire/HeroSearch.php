<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Course;

class HeroSearch extends Component
{
    public $search = '';

    public function getSlides()
    {
        return [
            [
                'title' => __('Tingkatkan Keahlian Anda Bersama'),
                'highlight' => __('Fakultas Terbaik'),
                'description' => __('Akses ribuan materi pembelajaran dari para ahli dan raih karir impian Anda. Temukan kursus yang tepat untuk Anda hari ini.'),
                'image' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1351&q=80',
                'gradient' => 'from-indigo-900/80 to-blue-900/60'
            ],
            [
                'title' => __('Sertifikasi Profesional dari'),
                'highlight' => __('Institusi Ternama'),
                'description' => __('Dapatkan sertifikat kompetensi yang diakui industri dan tingkatkan nilai jual Anda di dunia kerja.'),
                'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                'gradient' => 'from-slate-900/80 to-indigo-900/60'
            ],
            [
                'title' => __('Belajar Fleksibel Kapan Saja'),
                'highlight' => __('Di Mana Saja'),
                'description' => __('Platform hybrid yang mendukung pembelajaran daring maupun luring. Sesuaikan jadwal belajar dengan aktivitas Anda.'),
                'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
                'gradient' => 'from-blue-900/80 to-sky-900/60'
            ]
        ];
    }

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
            'slides' => $this->getSlides(),
        ]);
    }
}
