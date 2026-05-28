<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Belajar - Hybrid E-Learning</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        jakarta: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0b0f19;
            background-image: 
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(225,39%,30%,0.2) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(339,49%,30%,0.15) 0, transparent 50%);
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-nav {
            background: rgba(11, 15, 25, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glow-hover {
            transition: all 0.3s ease;
        }
        .glow-hover:hover {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="font-jakarta text-slate-200 min-h-screen">

    <!-- Premium Navigation -->
    <nav class="glass-nav sticky top-0 z-50 py-4 px-6 md:px-12 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-pink-500 flex items-center justify-center shadow-lg shadow-indigo-500/30">
                <span class="font-outfit font-black text-xl text-white">G</span>
            </div>
            <div>
                <h1 class="font-outfit font-bold text-lg tracking-wide text-white leading-none">GLACIER</h1>
                <span class="text-[10px] tracking-widest text-indigo-400 font-semibold font-outfit uppercase">Global Access For Independent Learning</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="hidden md:flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs text-slate-400 font-medium">Siswa Active: <strong class="text-slate-200">{{ Auth::user()->name }}</strong></span>
            </div>
            <a href="/admin/my-profile" class="text-xs font-semibold px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 transition duration-300">
                My Profile
            </a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-xs font-semibold px-4 py-2 rounded-lg bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white transition duration-300">
                    Keluar
                </button>
            </form>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-6 py-8 md:py-12">
        <!-- Toast Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 p-4 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-sm font-medium">{{ session('info') }}</span>
            </div>
        @endif

        {{-- Proactive Profile Incompleteness Banner --}}
        @if(!Auth::user()->isProfileComplete())
            @php $missing = Auth::user()->getMissingProfileFields(); @endphp
            <div class="mb-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center mt-0.5">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-amber-300">Profil Anda Belum Lengkap</p>
                    <p class="text-xs text-amber-400/70 mt-1">
                        Data berikut masih kosong: <strong class="text-amber-300">{{ implode(', ', $missing) }}</strong>.
                        Silakan lengkapi profil Anda agar dapat membeli atau mendaftar kursus.
                    </p>
                    <a href="/admin/my-profile" class="inline-flex items-center gap-1.5 mt-3 text-xs font-bold text-amber-950 bg-amber-400 hover:bg-amber-300 px-3.5 py-2 rounded-lg transition duration-200 shadow-sm active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Lengkapi Profil Sekarang
                    </a>
                </div>
            </div>
        @endif

        <!-- Enrolled Courses Section -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-outfit font-bold text-2xl text-white tracking-wide">Kursus Anda</h2>
                    <p class="text-xs text-slate-400">Mulailah belajar dari kursus mandiri atau sinkronisasi kelas Moodle Anda.</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300">
                    {{ $enrolledBatches->count() }} Terdaftar
                </span>
            </div>

            @if($enrolledBatches->isEmpty())
                <div class="glass-card rounded-2xl p-8 text-center border-dashed border-slate-700/50">
                    <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 border border-slate-700">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="font-outfit font-semibold text-lg text-white">Belum Ada Kursus</h3>
                    <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">Silakan lihat katalog kursus populer di bawah ini untuk memulai perjalanan belajar Anda!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrolledBatches as $batch)
                        @php $course = $batch->course; @endphp
                        <div class="glass-card rounded-2xl overflow-hidden hover:scale-[1.01] transition duration-300 flex flex-col justify-between group">
                            <div>
                                <a href="{{ route('course.detail', $course->slug) }}" class="relative h-44 w-full bg-slate-800 overflow-hidden block">
                                    @if($course->thumbnail)
                                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-slate-800 to-indigo-950 flex items-center justify-center relative">
                                            <span class="font-outfit font-bold text-5xl text-indigo-500/20 absolute -right-2 -bottom-4">LMS</span>
                                            <svg class="w-12 h-12 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                    @endif
                                    <!-- Source Badge -->
                                    <div class="absolute top-3 left-3 flex gap-2">
                                        @if($course->source === 'moodle')
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-amber-500 text-slate-950 shadow-lg">Glacier LMS</span>
                                        @else
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-indigo-500 text-white shadow-lg">Glacier</span>
                                        @endif
                                    </div>
                                </a>

                                <div class="p-6">
                                    @if($course->category)
                                        <span class="text-[10px] font-semibold text-indigo-400 uppercase tracking-widest">{{ $course->category->name }}</span>
                                    @endif
                                    <h3 class="font-outfit font-bold text-lg text-white mt-1 group-hover:text-indigo-300 transition duration-300 leading-tight">
                                        <a href="{{ route('course.detail', $course->slug) }}" class="hover:underline">
                                            {{ $course->title }}
                                        </a>
                                    </h3>
                                    <!-- Batch Name Badge -->
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <span class="inline-block text-[10px] font-semibold bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded">{{ $batch->name }}</span>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-2 line-clamp-2">{{ strip_tags($course->description) }}</p>
                                </div>
                            </div>

                            <div class="px-6 pb-6 pt-3 border-t border-slate-800/40">
                                @if(now() >= $batch->start_date)
                                    <form action="{{ route('courses.learn', $course->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full text-center text-xs font-semibold py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white glow-hover flex items-center justify-center gap-2 mb-2">
                                            @if($course->source === 'moodle')
                                                <span>Mulai Belajar (SSO Moodle)</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                            @else
                                                <span>Buka Materi Lokal</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            @endif
                                        </button>
                                    </form>
                                     @if(isset($certificates[$course->id]))
                                        <a href="{{ asset('storage/' . $certificates[$course->id]->file_path) }}" target="_blank" class="w-full text-center text-xs font-semibold py-3 px-4 rounded-xl border border-emerald-500/50 text-emerald-400 hover:bg-emerald-500/10 glow-hover flex items-center justify-center gap-2 mb-2">
                                            <span>Unduh Sertifikat</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        </a>

                                        @php
                                            $existingReview = \App\Models\CourseReview::where('user_id', Auth::id())->where('course_id', $course->id)->first();
                                        @endphp
                                        @if(!$existingReview)
                                            <button onclick="openReviewModal('{{ $course->id }}', '{{ addslashes($course->title) }}')" class="w-full text-center text-xs font-bold py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 glow-hover flex items-center justify-center gap-2">
                                                <span>Beri Ulasan</span>
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                            </button>
                                        @else
                                            <div class="w-full text-center text-xs font-semibold py-2.5 px-4 rounded-xl bg-slate-800 border border-slate-700 text-amber-400 flex items-center justify-center gap-1.5">
                                                <span>Rating Anda:</span>
                                                <div class="flex">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3.5 h-3.5 {{ $i <= $existingReview->rating ? 'text-amber-400' : 'text-slate-600' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                    @endfor
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <div class="w-full text-center text-xs font-semibold py-3 px-4 rounded-xl bg-slate-800/50 border border-slate-700/30 text-slate-400 flex flex-col items-center justify-center gap-1">
                                        <div class="flex items-center gap-1.5 text-amber-500">
                                            <svg class="w-4 h-4 flex-shrink-0 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <span class="font-bold">Belum Dimulai</span>
                                        </div>
                                        <span class="text-[10px] text-slate-500">Kelas Dimulai Pada: <strong class="text-slate-400 font-medium">{{ $batch->start_date->format('d M Y, H:i') }}</strong></span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Course Catalog Section -->
        <section>
            <div class="mb-6">
                <h2 class="font-outfit font-bold text-2xl text-white tracking-wide">Katalog Kursus</h2>
                <p class="text-xs text-slate-400">Pilih dari kursus berbayar berkualitas. Dapatkan akses instant menggunakan sistem Direct Checkout.</p>
            </div>

            @if($availableBatches->isEmpty())
                <div class="glass-card rounded-2xl p-8 text-center border-dashed border-slate-700/50">
                    <p class="text-sm text-slate-500 font-medium">Belum ada kelas baru yang tersedia saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableBatches as $batch)
                        @php $course = $batch->course; @endphp
                        <div class="glass-card rounded-2xl overflow-hidden hover:scale-[1.01] transition duration-300 flex flex-col justify-between group">
                            <div>
                                <a href="{{ route('course.detail', $course->slug) }}" class="relative h-44 w-full bg-slate-800 overflow-hidden block">
                                    @if($course->thumbnail)
                                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-slate-900 to-indigo-950 flex items-center justify-center relative">
                                            <span class="font-outfit font-bold text-5xl text-indigo-500/10 absolute -right-2 -bottom-4">COURSE</span>
                                            <svg class="w-12 h-12 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                    @endif
                                    <!-- Price Tag -->
                                    <div class="absolute bottom-3 right-3 bg-slate-950/80 backdrop-blur px-3 py-1 rounded-lg border border-slate-700/40 text-xs font-bold text-emerald-400">
                                        @if($course->price == 0)
                                            {{ __('Gratis') }}
                                        @else
                                            Rp {{ number_format($course->price, 0, ',', '.') }}
                                        @endif
                                    </div>
                                    <!-- Source Badge -->
                                    <div class="absolute top-3 left-3">
                                        @if($course->source === 'moodle')
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-amber-500 text-slate-950 shadow-lg">Glacier LMS</span>
                                        @else
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-indigo-500 text-white shadow-lg">Glacier</span>
                                        @endif
                                    </div>
                                </a>

                                <div class="p-6">
                                    @if($course->category)
                                        <span class="text-[10px] font-semibold text-indigo-400 uppercase tracking-widest">{{ $course->category->name }}</span>
                                    @endif
                                    <h3 class="font-outfit font-bold text-lg text-white mt-1 group-hover:text-indigo-300 transition duration-300 leading-tight">
                                        <a href="{{ route('course.detail', $course->slug) }}" class="hover:underline">
                                            {{ $course->title }}
                                        </a>
                                    </h3>
                                    <!-- Batch Name Badge -->
                                    <div class="mt-1.5 flex items-center gap-2">
                                        <span class="inline-block text-[10px] font-semibold bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 px-2 py-0.5 rounded">{{ $batch->name }}</span>
                                    </div>

                                    <!-- Batch info metrics -->
                                    <div class="mt-3 space-y-1 bg-slate-950/40 p-2.5 rounded-xl border border-slate-800/60 text-[11px] text-slate-400">
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500">Kuota Terisi:</span>
                                            <span class="font-semibold text-slate-300">{{ $batch->enrollments_count }} / {{ $batch->quota }} Peserta</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-500">Batas Pendaftaran:</span>
                                            <span class="font-semibold text-rose-400">{{ $batch->registration_end_date->format('d M Y') }}</span>
                                        </div>
                                    </div>

                                    <p class="text-xs text-slate-400 mt-3 line-clamp-3">{{ strip_tags($course->description) }}</p>
                                </div>
                            </div>

                            <div class="px-6 pb-6 pt-3 border-t border-slate-800/40">
                                @if(now() > $batch->registration_end_date)
                                    <div class="w-full text-center text-xs font-bold py-3 px-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400">
                                        Pendaftaran Ditutup
                                    </div>
                                @elseif($batch->enrollments_count >= $batch->quota)
                                    <div class="w-full text-center text-xs font-bold py-3 px-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400">
                                        Kuota Penuh
                                    </div>
                                @else
                                    <form action="{{ route('checkout') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="course_batch_id" value="{{ $batch->id }}">
                                        @if($course->price == 0)
                                            <button type="submit" class="w-full text-center text-xs font-bold py-3 px-4 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white glow-hover flex items-center justify-center gap-2">
                                                <span>Daftar Gratis</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                            </button>
                                        @else
                                            <button type="submit" class="w-full text-center text-xs font-bold py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white glow-hover flex items-center justify-center gap-2">
                                                <span>Beli Sekarang</span>
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </button>
                                        @endif
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <!-- Beautiful Premium Review Modal -->
    <div id="reviewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-md transition-all duration-300">
        <div class="glass-card w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl border border-slate-700/50 flex flex-col transform scale-95 transition-transform duration-300" id="reviewModalContainer">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/60 flex items-center justify-between">
                <h3 class="font-outfit font-bold text-lg text-white">Beri Ulasan Kursus</h3>
                <button onclick="closeReviewModal()" class="text-slate-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Form -->
            <form id="reviewForm" method="POST" action="">
                @csrf
                <div class="p-6 space-y-5">
                    <!-- Course Title Display -->
                    <div>
                        <span class="text-[10px] font-semibold text-indigo-400 uppercase tracking-widest block">KURSUS</span>
                        <h4 id="reviewCourseTitle" class="font-outfit font-bold text-white text-base mt-0.5"></h4>
                    </div>

                    <!-- Star Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pilih Rating</label>
                        <div class="flex items-center gap-2">
                            <input type="hidden" name="rating" id="reviewRatingInput" value="5">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setReviewRating({{ $i }})" onmouseenter="highlightStars({{ $i }})" onmouseleave="resetStars()" class="text-slate-600 hover:scale-110 transition duration-150 focus:outline-none" id="star-{{ $i }}">
                                    <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </button>
                            @endfor
                            <span class="text-sm font-semibold text-slate-400 ml-2" id="ratingText">Luar Biasa!</span>
                        </div>
                    </div>

                    <!-- Review Textarea -->
                    <div>
                        <label for="review_text" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Tulis Ulasan Anda (Opsional)</label>
                        <textarea name="review_text" id="review_text" rows="4" maxlength="1000" placeholder="Ceritakan pengalaman belajar Anda yang berharga..." class="w-full bg-slate-900/60 border border-slate-700/60 rounded-xl px-4 py-3 text-slate-200 text-sm focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-slate-500 transition duration-300 resize-none"></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/60 bg-slate-900/40 flex justify-end gap-3">
                    <button type="button" onclick="closeReviewModal()" class="text-xs font-semibold px-4 py-2.5 rounded-xl border border-slate-700 hover:bg-slate-800 text-slate-300 transition">
                        Batal
                    </button>
                    <button type="submit" class="text-xs font-bold px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white glow-hover">
                        Kirim Ulasan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Logic -->
    <script>
        let currentRating = 5;
        const ratingPhrases = {
            1: "Sangat Buruk",
            2: "Buruk",
            3: "Cukup Baik",
            4: "Sangat Baik",
            5: "Luar Biasa!"
        };

        function openReviewModal(courseId, courseTitle) {
            const modal = document.getElementById('reviewModal');
            const container = document.getElementById('reviewModalContainer');
            const title = document.getElementById('reviewCourseTitle');
            const form = document.getElementById('reviewForm');

            title.textContent = courseTitle;
            form.action = `/courses/${courseId}/reviews`;
            
            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95');
                container.classList.add('scale-100');
            }, 50);

            setReviewRating(5);
        }

        function closeReviewModal() {
            const modal = document.getElementById('reviewModal');
            const container = document.getElementById('reviewModalContainer');

            container.classList.remove('scale-100');
            container.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function setReviewRating(rating) {
            currentRating = rating;
            document.getElementById('reviewRatingInput').value = rating;
            document.getElementById('ratingText').textContent = ratingPhrases[rating];
            updateStarColors(rating);
        }

        function highlightStars(rating) {
            updateStarColors(rating);
        }

        function resetStars() {
            updateStarColors(currentRating);
        }

        function updateStarColors(rating) {
            for (let i = 1; i <= 5; i++) {
                const star = document.getElementById(`star-${i}`);
                if (i <= rating) {
                    star.classList.remove('text-slate-600');
                    star.classList.add('text-amber-400');
                } else {
                    star.classList.remove('text-amber-400');
                    star.classList.add('text-slate-600');
                }
            }
        }
    </script>
</body>
</html>
