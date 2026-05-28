<div>
    {{-- Proactive Profile Incompleteness Banner --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
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
                                Silakan lengkapi profil terlebih dahulu agar dapat mendaftar kursus.
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
    </div>

    {{-- Hero Section --}}
    <section class="relative bg-slate-900 text-white pt-12 pb-20 overflow-hidden">
        {{-- Decorative background --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-1/2 -right-1/4 w-[1000px] h-[1000px] rounded-full bg-sky-500/10 blur-3xl"></div>
            <div class="absolute -bottom-1/2 -left-1/4 w-[800px] h-[800px] rounded-full bg-indigo-500/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMSIgZmlsbD0icmdiYSg5NiwtMTc1LC0yNTUsMC4wNSkiLz48L3N2Zz4=')] opacity-20"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ url('/') }}#courses" class="inline-flex items-center text-sm font-semibold text-slate-400 hover:text-white transition group">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ __('Kembali ke Katalog') }}
                </a>
            </div>

            <div class="flex flex-col lg:flex-row gap-12 items-center">
                {{-- Course Info --}}
                <div class="w-full lg:w-3/5 order-2 lg:order-1">
                    <div class="flex items-center gap-3 mb-4">
                        @if($course->category)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-sky-500/20 text-sky-300 border border-sky-400/30 backdrop-blur-sm">
                                {{ $course->category->name }}
                            </span>
                        @endif
                        @if($course->unit)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wider uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-400/30 backdrop-blur-sm">
                                {{ $course->unit->name }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-3 leading-tight tracking-tight drop-shadow-lg">
                        {{ $course->title }}
                    </h1>

                    @if($course->reviews_count > 0)
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex text-amber-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= round($course->average_rating) ? 'text-amber-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-sm font-bold text-slate-200">{{ number_format($course->average_rating, 1) }} / 5</span>
                            <span class="text-xs text-slate-400">({{ $course->reviews_count }} Ulasan)</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 mb-6">
                            <div class="flex text-slate-600">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 text-slate-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-xs text-slate-500">(Belum Ada Ulasan)</span>
                        </div>
                    @endif

                    <div class="flex flex-wrap items-center gap-4 mb-8">
                        @if($course->source === 'local')
                            <div class="flex items-center text-sm text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-700 backdrop-blur-md shadow-sm">
                                <svg class="w-4 h-4 mr-2 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ __('Aplikasi Glacier') }}
                            </div>
                        @elseif($course->source === 'moodle')
                            <div class="flex items-center text-sm text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-700 backdrop-blur-md shadow-sm">
                                <svg class="w-4 h-4 mr-2 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                                {{ __('LMS Moodle') }}
                            </div>
                        @elseif($course->source === 'hybrid')
                            <div class="flex items-center text-sm text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-700 backdrop-blur-md shadow-sm">
                                <svg class="w-4 h-4 mr-2 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                                {{ __('Hybrid Learning') }}
                            </div>
                        @endif

                        <div class="flex items-center text-sm text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-lg border border-slate-700 backdrop-blur-md shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            {{ $course->modules->count() }} {{ __('Modul Pembelajaran') }}
                        </div>
                    </div>
                </div>

                {{-- Course Card / Pricing (Mobile: top, Desktop: right side sticky later) --}}
                <div class="w-full lg:w-2/5 order-1 lg:order-2">
                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-2xl relative z-10 transform lg:-mb-32">
                        <div class="aspect-video w-full bg-slate-100 relative group overflow-hidden">
                            @if($course->thumbnail)
                                <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                    <svg class="h-12 w-12 stroke-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>

                        <div class="p-6 sm:p-8">
                            <div class="flex items-end justify-between mb-6">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">{{ __('Investasi') }}</p>
                                    <div class="text-3xl font-extrabold text-slate-900">
                                        @if($course->price == 0)
                                            <span class="text-emerald-600">{{ __('Gratis') }}</span>
                                        @else
                                            @if(app()->getLocale() == 'en')
                                                IDR {{ number_format($course->price, 0, '.', ',') }}
                                            @else
                                                Rp {{ number_format($course->price, 0, ',', '.') }}
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($batch)
                                <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wide">{{ __('Pendaftaran Terbuka') }}</span>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $batch->name }}</p>
                                    @if($batch->registration_end_date)
                                        <p class="text-xs text-slate-500 mt-1">{{ __('Tutup pada:') }} {{ $batch->registration_end_date->format('d M Y') }}</p>
                                    @endif
                                </div>

                                @auth
                                    @php
                                        $isEnrolled = Auth::user()->enrolledBatches->contains($batch->id);
                                    @endphp

                                    @if($isEnrolled)
                                        <a href="{{ route('dashboard') }}" class="flex items-center justify-center w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold py-3.5 px-4 rounded-xl transition duration-200 border border-slate-300">
                                            {{ __('Masuk Kelas') }}
                                        </a>
                                    @else
                                        <form action="{{ route('checkout') }}" method="POST" class="w-full">
                                            @csrf
                                            <input type="hidden" name="course_batch_id" value="{{ $batch->id }}">
                                            @if($course->price == 0)
                                                <button type="submit" class="flex items-center justify-center w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-emerald-600/30 transition duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                                                    {{ __('Daftar Sekarang (Gratis)') }}
                                                </button>
                                            @else
                                                <button type="submit" class="flex items-center justify-center w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-sky-600/30 transition duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                                                    {{ __('Beli Kelas Ini') }}
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center justify-center w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-3.5 px-4 rounded-xl shadow-lg shadow-slate-900/30 transition duration-200 transform hover:-translate-y-0.5 active:translate-y-0">
                                        {{ __('Login untuk Mendaftar') }}
                                    </a>
                                @endauth
                            @else
                                <div class="bg-amber-50 border border-amber-200 text-amber-800 text-sm font-semibold p-4 rounded-xl flex items-center justify-center text-center">
                                    {{ __('Mohon maaf, saat ini belum ada jadwal kelas yang tersedia.') }}
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Main Content Section --}}
    <section class="py-16 bg-white relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-12">
                {{-- Left Content --}}
                <div class="w-full lg:w-3/5 lg:pr-8">
                    {{-- Deskripsi --}}
                    <div class="mb-12">
                        <h2 class="text-2xl font-extrabold text-slate-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-sky-100 flex items-center justify-center text-sky-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            {{ __('Tentang Kelas Ini') }}
                        </h2>
                        
                        <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed">
                            @if($course->description)
                                {!! $course->description !!}
                            @else
                                <p>{{ __('Belum ada deskripsi untuk kursus ini.') }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Kurikulum / Modul --}}
                    <div class="mb-12">
                        <h2 class="text-2xl font-extrabold text-slate-900 mb-6 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </span>
                            {{ __('Materi Pembelajaran') }}
                        </h2>

                        @if($course->modules->count() > 0)
                            <div class="space-y-4" x-data="{ activeModule: null }">
                                @foreach($course->modules as $index => $module)
                                    <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-sm transition-all duration-200"
                                         :class="{ 'border-sky-300 ring-4 ring-sky-50': activeModule === {{ $index }} }">
                                        <button @click="activeModule = activeModule === {{ $index }} ? null : {{ $index }}" 
                                                class="w-full px-6 py-4 flex items-center justify-between bg-slate-50 hover:bg-slate-100 transition-colors">
                                            <div class="flex items-center gap-4">
                                                <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center font-bold text-slate-700 shadow-sm border border-slate-200">
                                                    {{ $index + 1 }}
                                                </div>
                                                <h3 class="text-base font-bold text-slate-900 text-left">
                                                    {{ $module->title }}
                                                </h3>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="text-xs font-semibold text-slate-500 hidden sm:block">
                                                    {{ $module->lessons->count() }} {{ __('Lesson') }}
                                                </span>
                                                <svg class="w-5 h-5 text-slate-400 transform transition-transform duration-200" 
                                                     :class="{ 'rotate-180': activeModule === {{ $index }} }"
                                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </button>
                                        
                                        <div x-show="activeModule === {{ $index }}" x-collapse x-cloak>
                                            <div class="p-6 bg-white border-t border-slate-100">
                                                @if($module->description)
                                                    <p class="text-sm text-slate-600 mb-4">{{ $module->description }}</p>
                                                @endif

                                                @if($module->lessons->count() > 0)
                                                    <ul class="space-y-3">
                                                        @foreach($module->lessons as $lesson)
                                                            <li class="flex items-start gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                                                                <svg class="w-5 h-5 text-sky-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                                <div>
                                                                    <p class="text-sm font-semibold text-slate-800">{{ $lesson->title }}</p>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="text-sm text-slate-500 italic">{{ __('Belum ada materi untuk modul ini.') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center">
                                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <p class="text-slate-500 font-medium">{{ __('Kurikulum sedang dipersiapkan.') }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Ulasan Siswa --}}
                    <div class="mt-16 pt-12 border-t border-slate-200">
                        <h2 class="text-2xl font-extrabold text-slate-900 mb-8 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            </span>
                            {{ __('Ulasan Siswa') }}
                        </h2>

                        @php
                            $publishedReviews = $course->reviews()->where('status', 'published')->with('user')->latest()->get();
                        @endphp

                        @if($publishedReviews->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10 items-start">
                                <!-- Aggregate Summary -->
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center">
                                    <div class="text-5xl font-black text-slate-900 leading-none mb-2">
                                        {{ number_format($course->average_rating, 1) }}
                                    </div>
                                    <div class="flex justify-center text-amber-400 mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= round($course->average_rating) ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Berdasarkan {{ $publishedReviews->count() }} Ulasan</span>
                                </div>

                                <!-- Rating Bars -->
                                <div class="md:col-span-2 space-y-2">
                                    @for($r = 5; $r >= 1; $r--)
                                        @php
                                            $count = $publishedReviews->where('rating', $r)->count();
                                            $percentage = $publishedReviews->count() > 0 ? ($count / $publishedReviews->count()) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-slate-600 w-3">{{ $r }}</span>
                                            <svg class="w-3.5 h-3.5 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-amber-400 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-500 w-8 text-right">{{ $count }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Individual Reviews List -->
                            <div class="space-y-6">
                                @foreach($publishedReviews as $review)
                                    <div class="p-6 border border-slate-200 rounded-2xl bg-white shadow-sm flex flex-col gap-4">
                                        <div class="flex justify-between items-start gap-4 flex-wrap">
                                            <div class="flex items-center gap-3">
                                                <!-- User Avatar / Fallback -->
                                                <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center font-bold text-sky-700 shadow-inner">
                                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <h4 class="text-sm font-bold text-slate-800">{{ $review->user->name }}</h4>
                                                    <span class="text-[10px] text-slate-400">{{ $review->created_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <!-- Stars -->
                                            <div class="flex text-amber-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endfor
                                            </div>
                                        </div>

                                        <!-- Review Text -->
                                        @if($review->review_text)
                                            <p class="text-sm text-slate-600 leading-relaxed italic">
                                                "{{ $review->review_text }}"
                                            </p>
                                        @else
                                            <p class="text-xs text-slate-400 italic">
                                                (Hanya memberikan rating bintang)
                                            </p>
                                        @endif

                                        <!-- Teacher/Admin Reply -->
                                        @if($review->reply_text)
                                            <div class="mt-2 pl-4 py-3 border-l-2 border-indigo-500 bg-indigo-50/50 rounded-r-xl">
                                                <div class="flex items-center gap-1.5 text-indigo-700 mb-1">
                                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a.75.75 0 00-.708-.523H4.5a.75.75 0 00-.75.75v12.651a.75.75 0 00.75.75h1a.75.75 0 00.708-.523l1.812-5.568a.75.75 0 01.708-.523h4.256a.75.75 0 00.708-.523l1.812-5.568a.75.75 0 01.708-.523h-8.08a.75.75 0 00-.708-.523z" clip-rule="evenodd"></path></svg>
                                                    <span class="text-xs font-bold uppercase tracking-wider">Tanggapan Resmi Pengajar</span>
                                                </div>
                                                <p class="text-xs text-slate-700 leading-relaxed">
                                                    {{ $review->reply_text }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-8 text-center">
                                <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <p class="text-slate-500 font-medium">{{ __('Belum ada ulasan untuk kelas ini.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
