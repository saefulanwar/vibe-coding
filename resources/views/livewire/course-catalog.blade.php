<section id="courses" class="py-20 bg-slate-50" x-data="{ mobileFilterOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Proactive Profile Incompleteness Banner --}}
        @auth
            @if(!Auth::user()->isProfileComplete())
                @php $missing = Auth::user()->getMissingProfileFields(); @endphp
                <div class="mb-8 relative overflow-hidden rounded-2xl border border-amber-300/50 bg-gradient-to-r from-amber-50 via-orange-50 to-amber-50 p-5 shadow-sm">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSgyNDUsMTU4LDExLDAuMSkiLz48L3N2Zz4=')] opacity-50"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-amber-500/15 flex items-center justify-center border border-amber-400/30">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-amber-900">Profil Anda Belum Lengkap</h4>
                            <p class="text-sm text-amber-800/80 mt-1">
                                Data berikut masih kosong: <strong>{{ implode(', ', $missing) }}</strong>.
                                Silakan lengkapi profil terlebih dahulu agar dapat memilih kursus.
                            </p>
                            <a href="/admin/my-profile" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 border border-amber-300/60 px-3.5 py-2 rounded-lg transition duration-200 active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Lengkapi Profil Sekarang
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
            <div class="text-center w-full max-w-2xl mx-auto mb-6">
                <span class="inline-block text-xs font-bold text-sky-600 tracking-widest uppercase bg-sky-50 px-3 py-1 rounded-full shadow-sm border border-sky-100">
                    {{ __('Katalog Kursus') }}
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-3">
                    {{ __('Available Courses') }}
                </h2>
                <p class="mt-2 text-slate-600">
                    {{ __('Jelajahi berbagai materi pilihan dari kami.') }}
                </p>
            </div>
        </div>

        <div class="flex justify-end mb-4 md:hidden">
            <button @click="mobileFilterOpen = true" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition active:scale-95">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                </svg>
                {{ __('Filter & Urutkan') }}
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 items-start">
            
            <aside class="hidden lg:block w-1/4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm sticky top-24">
                <div class="flex items-center justify-between pb-4 mb-5 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Saring Pencarian') }}</h3>
                    @if($search || $selectedUnit || $priceFilter !== 'all' || $deliveryFilter !== 'all')
                        <button wire:click="$set('search', ''); $set('selectedUnit', ''); $set('priceFilter', 'all'); $set('deliveryFilter', 'all');" class="text-xs font-semibold text-rose-600 hover:text-rose-700 transition">{{ __('Reset') }}</button>
                    @endif
                </div>
                
                <div class="mb-5">
                    <label for="search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Kata Kunci') }}</label>
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="search" type="text" id="search" class="w-full text-sm pl-4 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition placeholder-slate-400" placeholder="{{ __('Cari judul kursus...') }}">
                    </div>
                </div>

                <div class="mb-5 border-t border-slate-100 pt-4">
                    <label for="unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Unit / Fakultas') }}</label>
                    <select wire:model.live="selectedUnit" id="unit" class="w-full text-sm bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition">
                        <option value="">{{ __('Semua Unit') }}</option>
                        @foreach($this->units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5 border-t border-slate-100 pt-4">
                    <label for="delivery" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Metode Belajar') }}</label>
                    <select wire:model.live="deliveryFilter" id="delivery" class="w-full text-sm bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition">
                        <option value="all">{{ __('Semua Metode') }}</option>
                        <option value="local">{{ __('Aplikasi Glacier') }}</option>
                        <option value="moodle">{{ __('LMS Moodle') }}</option>
                        <option value="hybrid">{{ __('Hybrid Learning') }}</option>
                    </select>
                </div>

                <div class="mb-5 border-t border-slate-100 pt-4">
                    <label for="price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Harga') }}</label>
                    <select wire:model.live="priceFilter" id="price" class="w-full text-sm bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition">
                        <option value="all">{{ __('Semua Harga') }}</option>
                        <option value="free">{{ __('Gratis') }}</option>
                        <option value="paid">{{ __('Berbayar') }}</option>
                    </select>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <label for="sort" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Urutkan') }}</label>
                    <select wire:model.live="sortBy" id="sort" class="w-full text-sm bg-slate-50/50 border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-4 focus:ring-sky-500/10 focus:border-sky-500 transition">
                        <option value="newest">{{ __('Terbaru') }}</option>
                        <option value="price_asc">{{ __('Harga Terendah') }}</option>
                        <option value="price_desc">{{ __('Harga Tinggi') }}</option>
                    </select>
                </div>
            </aside>

            <div class="w-full lg:w-3/4">
                @if($this->batches->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 items-start">
                        @foreach($this->batches as $batch)
                            <div class="bg-white rounded-2xl border border-slate-200/70 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col h-auto group">
                                
                                <div class="relative aspect-video w-full bg-slate-100 overflow-hidden flex-shrink-0">
                                    @if($batch->course->thumbnail)
                                        <img src="{{ $batch->course->thumbnail }}" alt="{{ $batch->course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                            <svg class="h-8 w-8 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($batch->course->unit)
                                        <div class="absolute top-3 left-3 bg-slate-900/80 backdrop-blur-md text-white text-[10px] font-bold tracking-wider uppercase px-2.5 py-1 rounded-md shadow-sm">
                                            {{ $batch->course->unit->name }}
                                        </div>
                                    @endif
                                </div>

                                <div class="p-5 flex flex-col justify-between flex-grow">
                                    <div>
                                        <div class="flex items-center gap-1.5 mb-2">
                                            <span class="text-xs font-bold text-amber-500 uppercase tracking-wide">{{ $batch->name }}</span>
                                            <span class="text-slate-300 text-xs">•</span>
                                            <span class="text-xs text-slate-500">{{ __('Umum') }}</span>
                                        </div>

                                        <h3 class="text-base font-bold text-slate-900 mb-1 group-hover:text-sky-600 transition duration-150 leading-snug min-h-[2.75rem] line-clamp-2">
                                            {{ $batch->course->title }}
                                        </h3>

                                        <div class="flex flex-wrap items-center gap-1.5 mb-3">
                                            @if($batch->course->source === 'local')
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200/60 px-2 py-0.5 rounded-md shadow-2xs">
                                                    <span class="w-1 h-1 rounded-full bg-sky-500 animate-pulse"></span> {{ __('Aplikasi Glacier') }}
                                                </span>
                                            @elseif($batch->course->source === 'moodle')
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-orange-700 bg-orange-50 border border-orange-200/60 px-2 py-0.5 rounded-md shadow-2xs">
                                                    <span class="w-1 h-1 rounded-full bg-orange-500"></span> {{ __('LMS Moodle') }}
                                                </span>
                                            @elseif($batch->course->source === 'hybrid')
                                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-700 bg-indigo-50 border border-indigo-200/60 px-2 py-0.5 rounded-md shadow-2xs">
                                                    <span class="w-1 h-1 rounded-full bg-indigo-500"></span> {{ __('Hybrid Learning') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-xs text-slate-500 mb-5 leading-relaxed min-h-[3rem]">
                                            @if($batch->course->description)
                                                <div class="line-clamp-3">
                                                    {!! Str::limit(strip_tags($batch->course->description), 120, '...') !!}
                                                </div>
                                            @else
                                                <div class="line-clamp-3">
                                                    {{ __('Informasi silabus dan pokok bahasan materi perkuliahan belum diisi oleh unit.') }}
                                                </div>
                                            @endif
                                            
                                            <!-- Clean Fallback System footnote for English readers -->
                                            @if(app()->getLocale() == 'en')
                                                <div class="mt-2 text-[10px] text-slate-400 italic">
                                                    * {{ __('Content only available in Indonesian.') }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2 mt-auto">
                                        <div>
                                            <span class="block text-[10px] text-slate-400 font-bold uppercase tracking-wider leading-none">{{ __('Investasi') }}</span>
                                            <div class="font-extrabold text-base text-slate-900 mt-1">
                                                @if($batch->course->price == 0)
                                                    <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded text-xs border border-emerald-200/50">{{ __('Gratis') }}</span>
                                                @else
                                                    @if(app()->getLocale() == 'en')
                                                        IDR {{ number_format($batch->course->price, 0, '.', ',') }}
                                                    @else
                                                        Rp {{ number_format($batch->course->price, 0, ',', '.') }}
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @auth
                                            @php
                                                $isEnrolled = Auth::user()->enrolledBatches->contains($batch->id);
                                            @endphp

                                            @if($isEnrolled)
                                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl transition duration-200 shadow-sm border border-slate-200/80 active:scale-95">
                                                    {{ __('Ke Dashboard') }}
                                                </a>
                                            @else
                                                <form action="{{ route('checkout') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="course_batch_id" value="{{ $batch->id }}">
                                                    @if($batch->course->price == 0)
                                                        <button type="submit" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition duration-200 shadow-md border border-emerald-500/20 active:scale-95">
                                                            {{ __('Daftar Gratis') }}
                                                        </button>
                                                    @else
                                                        <button type="submit" class="inline-flex items-center justify-center bg-slate-900 hover:bg-sky-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition duration-200 group-hover:shadow-md active:scale-95">
                                                            {{ __('Beli Sekarang') }}
                                                        </button>
                                                    @endif
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-slate-900 hover:bg-sky-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition duration-200 group-hover:shadow-md">
                                                {{ __('Daftar Kelas') }}
                                            </a>
                                        @endauth
                                    </div>
                                </div>

                             </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-12 border-t border-slate-200/60 pt-6">
                        {{ $this->batches->links() }}
                    </div>
                @else
                    <div class="bg-white rounded-2xl border border-slate-200/80 p-12 text-center max-w-md mx-auto shadow-sm">
                        <div class="w-14 h-14 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-inner">
                            <svg class="h-6 w-6 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1">{{ __('Kursus Tidak Ditemukan') }}</h3>
                        <p class="text-xs text-slate-500 mb-6 max-w-xs mx-auto">{{ __('Kata kunci atau filter yang Anda pilih tidak cocok dengan kelas kompetensi mana pun.') }}</p>
                        <button wire:click="$set('search', ''); $set('selectedUnit', ''); $set('priceFilter', 'all'); $set('deliveryFilter', 'all');" class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition shadow-sm">
                            {{ __('Reset Semua Filter') }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="mobileFilterOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden lg:hidden" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div @click="mobileFilterOpen = false" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" x-show="mobileFilterOpen" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="pointer-events-auto w-screen max-w-sm" x-show="mobileFilterOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white py-6 shadow-xl">
                        <div class="px-4 sm:px-6 flex items-center justify-between pb-4 border-b border-slate-100">
                            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Saring Pencarian') }}</h2>
                            <button @click="mobileFilterOpen = false" class="rounded-md text-slate-400 hover:text-slate-500 p-1 bg-slate-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-5">
                            <div>
                                <label for="m-search" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Kata Kunci') }}</label>
                                <input wire:model.live.debounce.300ms="search" type="text" id="m-search" class="w-full text-sm px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl" placeholder="{{ __('Cari') }}...">
                            </div>
                            <div>
                                <label for="m-unit" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Unit / Fakultas') }}</label>
                                <select wire:model.live="selectedUnit" id="m-unit" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                                    <option value="">{{ __('Semua Unit') }}</option>
                                    @foreach($this->units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="m-delivery" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Metode Belajar') }}</label>
                                <select wire:model.live="deliveryFilter" id="m-delivery" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                                    <option value="all">{{ __('Semua Metode') }}</option>
                                    <option value="local">{{ __('Aplikasi Glacier') }}</option>
                                    <option value="moodle">{{ __('LMS Moodle') }}</option>
                                    <option value="hybrid">{{ __('Hybrid Learning') }}</option>
                                </select>
                            </div>
                            <div>
                                <label for="m-price" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">{{ __('Harga') }}</label>
                                <select wire:model.live="priceFilter" id="m-price" class="w-full text-sm bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5">
                                    <option value="all">{{ __('Semua Harga') }}</option>
                                    <option value="free">{{ __('Gratis') }}</option>
                                    <option value="paid">{{ __('Berbayar') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>