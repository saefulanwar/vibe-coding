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
                <span class="font-outfit font-black text-xl text-white">V</span>
            </div>
            <div>
                <h1 class="font-outfit font-bold text-lg tracking-wide text-white leading-none">VIBELEARN</h1>
                <span class="text-[10px] tracking-widest text-indigo-400 font-semibold font-outfit uppercase">Hybrid LMS</span>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="hidden md:flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-xs text-slate-400 font-medium">Siswa Active: <strong class="text-slate-200">{{ Auth::user()->name }}</strong></span>
            </div>
            <a href="/admin" class="text-xs font-semibold px-4 py-2 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-700 transition duration-300">
                Admin Panel
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

        <!-- Enrolled Courses Section -->
        <section class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-outfit font-bold text-2xl text-white tracking-wide">Kursus Anda</h2>
                    <p class="text-xs text-slate-400">Mulailah belajar dari kursus mandiri atau sinkronisasi kelas Moodle Anda.</p>
                </div>
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/30 text-indigo-300">
                    {{ $enrolledCourses->count() }} Terdaftar
                </span>
            </div>

            @if($enrolledCourses->isEmpty())
                <div class="glass-card rounded-2xl p-8 text-center border-dashed border-slate-700/50">
                    <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 border border-slate-700">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="font-outfit font-semibold text-lg text-white">Belum Ada Kursus</h3>
                    <p class="text-sm text-slate-400 mt-1 max-w-sm mx-auto">Silakan lihat katalog kursus populer di bawah ini untuk memulai perjalanan belajar Anda!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($enrolledCourses as $course)
                        <div class="glass-card rounded-2xl overflow-hidden hover:scale-[1.01] transition duration-300 flex flex-col justify-between group">
                            <div>
                                <div class="relative h-44 w-full bg-slate-800 overflow-hidden">
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
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-amber-500 text-slate-950 shadow-lg">Moodle LMS</span>
                                        @else
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-indigo-500 text-white shadow-lg">Local</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-6">
                                    @if($course->category)
                                        <span class="text-[10px] font-semibold text-indigo-400 uppercase tracking-widest">{{ $course->category->name }}</span>
                                    @endif
                                    <h3 class="font-outfit font-bold text-lg text-white mt-1 group-hover:text-indigo-300 transition duration-300 leading-tight">
                                        {{ $course->title }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-2 line-clamp-2">{{ strip_tags($course->description) }}</p>
                                </div>
                            </div>

                            <div class="px-6 pb-6 pt-3 border-t border-slate-800/40">
                                <form action="{{ route('courses.learn', $course->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-center text-xs font-semibold py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white glow-hover flex items-center justify-center gap-2">
                                        @if($course->source === 'moodle')
                                            <span>Mulai Belajar (SSO Moodle)</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        @else
                                            <span>Buka Materi Lokal</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        @endif
                                    </button>
                                </form>
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

            @if($availableCourses->isEmpty())
                <div class="glass-card rounded-2xl p-8 text-center border-dashed border-slate-700/50">
                    <p class="text-sm text-slate-500 font-medium">Belum ada kelas baru yang tersedia saat ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($availableCourses as $course)
                        <div class="glass-card rounded-2xl overflow-hidden hover:scale-[1.01] transition duration-300 flex flex-col justify-between group">
                            <div>
                                <div class="relative h-44 w-full bg-slate-800 overflow-hidden">
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
                                        Rp {{ number_format($course->price, 0, ',', '.') }}
                                    </div>
                                    <!-- Source Badge -->
                                    <div class="absolute top-3 left-3">
                                        @if($course->source === 'moodle')
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-amber-500 text-slate-950 shadow-lg">Moodle LMS</span>
                                        @else
                                            <span class="text-[10px] font-bold tracking-wide uppercase px-2 py-1 rounded bg-indigo-500 text-white shadow-lg">Local</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="p-6">
                                    @if($course->category)
                                        <span class="text-[10px] font-semibold text-indigo-400 uppercase tracking-widest">{{ $course->category->name }}</span>
                                    @endif
                                    <h3 class="font-outfit font-bold text-lg text-white mt-1 group-hover:text-indigo-300 transition duration-300 leading-tight">
                                        {{ $course->title }}
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-2 line-clamp-3">{{ strip_tags($course->description) }}</p>
                                </div>
                            </div>

                            <div class="px-6 pb-6 pt-3 border-t border-slate-800/40">
                                <form action="{{ route('checkout') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                                    <button type="submit" class="w-full text-center text-xs font-bold py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white glow-hover flex items-center justify-center gap-2">
                                        <span>Beli Sekarang</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

</body>
</html>
