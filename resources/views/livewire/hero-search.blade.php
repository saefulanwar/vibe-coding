<div>
<div class="relative" x-data="{
    currentSlide: 0,
    totalSlides: 3,
    autoplayInterval: null,
    slides: [
        {
            title: @json(__('Tingkatkan Keahlian Anda Bersama')),
            highlight: @json(__('Fakultas Terbaik')),
            description: @json(__('Akses ribuan materi pembelajaran dari para ahli dan raih karir impian Anda. Temukan kursus yang tepat untuk Anda hari ini.')),
            image: 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1351&q=80',
            gradient: 'from-indigo-900/80 to-blue-900/60'
        },
        {
            title: @json(__('Sertifikasi Profesional dari')),
            highlight: @json(__('Institusi Ternama')),
            description: @json(__('Dapatkan sertifikat kompetensi yang diakui industri dan tingkatkan nilai jual Anda di dunia kerja.')),
            image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
            gradient: 'from-slate-900/80 to-indigo-900/60'
        },
        {
            title: @json(__('Belajar Fleksibel Kapan Saja')),
            highlight: @json(__('Di Mana Saja')),
            description: @json(__('Platform hybrid yang mendukung pembelajaran daring maupun luring. Sesuaikan jadwal belajar dengan aktivitas Anda.')),
            image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80',
            gradient: 'from-blue-900/80 to-sky-900/60'
        }
    ],
    next() { this.currentSlide = (this.currentSlide + 1) % this.totalSlides },
    prev() { this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides },
    startAutoplay() {
        this.autoplayInterval = setInterval(() => { this.next() }, 5000)
    },
    stopAutoplay() { clearInterval(this.autoplayInterval) }
}" x-init="startAutoplay()" x-on:mouseenter="stopAutoplay()" x-on:mouseleave="startAutoplay()">

    <!-- Slides -->
    <div class="relative h-[520px] sm:h-[560px] lg:h-[600px] overflow-hidden">
        <template x-for="(slide, index) in slides" :key="index">
            <div 
                x-show="currentSlide === index"
                x-transition:enter="transition ease-out duration-700"
                x-transition:enter-start="opacity-0 scale-105"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-500"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute inset-0"
            >
                <!-- Background Image -->
                <img :src="slide.image" :alt="slide.title" class="absolute inset-0 w-full h-full object-cover">
                
                <!-- Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-r" :class="slide.gradient"></div>
                
                <!-- Content -->
                <div class="relative z-10 h-full flex items-center">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                        <div class="max-w-2xl">
                            <h1 class="text-4xl tracking-tight font-extrabold text-white sm:text-5xl md:text-6xl">
                                <span class="block" x-text="slide.title"></span>
                                <span class="block text-sky-400 mt-2" x-text="slide.highlight"></span>
                            </h1>
                            <p class="mt-4 text-lg text-slate-200 sm:text-xl max-w-xl" x-text="slide.description"></p>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Navigation Arrows -->
    <button x-on:click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-3 rounded-full transition duration-300 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
    </button>
    <button x-on:click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 z-20 bg-white/20 hover:bg-white/40 backdrop-blur-sm text-white p-3 rounded-full transition duration-300 shadow-lg">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
    </button>

    <!-- Dot Indicators -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-20 flex space-x-3">
        <template x-for="(slide, index) in slides" :key="'dot-'+index">
            <button 
                x-on:click="currentSlide = index"
                :class="currentSlide === index ? 'bg-white w-8' : 'bg-white/50 w-3 hover:bg-white/80'"
                class="h-3 rounded-full transition-all duration-300"
            ></button>
        </template>
    </div>

    <!-- Search Bar Overlay -->
    <div class="absolute bottom-0 left-0 right-0 z-20 translate-y-1/2">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative bg-white rounded-2xl shadow-2xl p-2 border border-slate-200">
                <div class="flex items-center">
                    <div class="pl-4">
                        <svg class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" class="flex-1 px-4 py-4 text-lg border-0 focus:ring-0 focus:outline-none placeholder-slate-400 bg-transparent" placeholder="{{ __('Cari kursus, keterampilan, atau topik...') }}">
                    <button class="bg-sky-600 hover:bg-sky-700 text-white px-8 py-3 rounded-xl font-semibold transition duration-300 text-sm">
                        {{ __('Cari') }}
                    </button>
                </div>
                
                <!-- Search Results Dropdown -->
                @if(strlen($search) >= 2)
                    <div class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-lg border border-slate-200 max-h-80 overflow-y-auto z-50">
                        @if(count($results) > 0)
                            <ul class="py-2">
                                @foreach($results as $course)
                                    <li>
                                        <a href="#courses" class="flex items-center px-4 py-3 hover:bg-slate-50 transition">
                                            @if($course->thumbnail)
                                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="h-10 w-10 rounded-lg object-cover mr-3">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-sky-100 flex items-center justify-center mr-3 text-sky-600">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-slate-900">{{ $course->title }}</div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $course->category?->name ?? __('Umum') }} · 
                                                    @if(app()->getLocale() == 'en')
                                                        IDR {{ number_format($course->price, 0, '.', ',') }}
                                                    @else
                                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="px-4 py-6 text-center text-slate-500">
                                {{ __('Tidak ada kursus ditemukan untuk ":search".', ['search' => $search]) }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Spacer for the overlapping search bar -->
<div class="h-12"></div>
</div>
