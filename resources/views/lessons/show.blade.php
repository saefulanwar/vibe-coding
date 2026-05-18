<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $currentLesson->title }} - {{ $course->title }}</title>
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
                radial-gradient(at 100% 100%, hsla(225,39%,30%,0.15) 0, transparent 50%);
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-sidebar {
            background: rgba(11, 15, 25, 0.85);
            backdrop-filter: blur(16px);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-nav {
            background: rgba(11, 15, 25, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="font-jakarta text-slate-200 min-h-screen flex flex-col">

    <!-- Premium Navigation Header -->
    <nav class="glass-nav py-4 px-6 md:px-12 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="p-2 rounded-xl bg-slate-800/80 border border-slate-700/50 hover:bg-slate-700 transition duration-300 text-slate-300 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            </a>
            <div>
                <span class="text-[10px] tracking-widest text-indigo-400 font-semibold uppercase leading-none block">Belajar Mandiri</span>
                <h1 class="font-outfit font-bold text-base text-white leading-tight mt-0.5 line-clamp-1">{{ $course->title }}</h1>
            </div>
        </div>
        <div>
            <a href="{{ route('dashboard') }}" class="text-xs font-semibold px-4 py-2.5 rounded-xl bg-indigo-500 hover:bg-indigo-600 transition text-white shadow-lg shadow-indigo-500/20">
                Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <!-- Main Workspace -->
    <div class="flex-grow flex flex-col lg:flex-row">
        
        <!-- Left Column: Syllabus Sidebar -->
        <aside class="w-full lg:w-80 glass-sidebar flex-shrink-0 p-6 overflow-y-auto lg:max-h-[calc(100vh-73px)] sticky top-[73px]">
            <h2 class="font-outfit font-bold text-sm tracking-widest text-slate-400 uppercase mb-4">Materi Kursus</h2>

            <div class="space-y-4">
                @foreach($course->modules as $index => $module)
                    <div class="space-y-2">
                        <!-- Module Header -->
                        <div class="flex items-center gap-2 pb-1.5 border-b border-slate-800">
                            <span class="w-5 h-5 rounded bg-indigo-500/10 text-indigo-400 font-semibold text-xs flex items-center justify-center border border-indigo-500/20">
                                {{ $index + 1 }}
                            </span>
                            <h3 class="font-outfit font-bold text-xs text-white uppercase tracking-wider line-clamp-1">{{ $module->title }}</h3>
                        </div>

                        <!-- Lessons List -->
                        <ul class="space-y-1 pl-7">
                            @foreach($module->lessons as $lesson)
                                <li>
                                    <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                                       class="flex items-center gap-2 py-2 px-3 rounded-lg text-xs font-medium transition duration-300
                                       {{ $lesson->id == $currentLesson->id 
                                          ? 'bg-indigo-500/15 border border-indigo-500/30 text-indigo-300 font-semibold' 
                                          : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                                        
                                        <!-- Play/Doc Icon -->
                                        @if($lesson->video_url)
                                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        @endif
                                        <span class="truncate">{{ $lesson->title }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- Right Column: Content Body -->
        <main class="flex-grow p-6 md:p-10 max-w-5xl mx-auto w-full lg:max-h-[calc(100vh-73px)] lg:overflow-y-auto">
            
            <!-- Video Player Area -->
            @if($currentLesson->video_url)
                <div class="glass-card rounded-3xl overflow-hidden shadow-2xl mb-8 border border-slate-700/30">
                    <div class="relative w-full aspect-video bg-slate-950 flex items-center justify-center">
                        <!-- Try to render iframe if youtube/vimeo, else HTML5 player -->
                        @if(Str::contains($currentLesson->video_url, ['youtube.com', 'youtu.be']))
                            @php
                                $videoId = '';
                                if(preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $currentLesson->video_url, $match)) {
                                    $videoId = $match[1];
                                }
                            @endphp
                            @if($videoId)
                                <iframe class="absolute inset-0 w-full h-full border-0" src="https://www.youtube.com/embed/{{ $videoId }}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                            @else
                                <div class="text-center p-6">
                                    <p class="text-sm text-slate-400">Video Player (YouTube)</p>
                                    <a href="{{ $currentLesson->video_url }}" target="_blank" class="text-indigo-400 font-semibold hover:underline mt-2 inline-block">{{ $currentLesson->video_url }}</a>
                                </div>
                            @endif
                        @else
                            <video class="absolute inset-0 w-full h-full object-cover" controls preload="metadata">
                                <source src="{{ $currentLesson->video_url }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutar video HTML5.
                            </video>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Lesson Info & Text Content -->
            <div class="glass-card rounded-3xl p-6 md:p-8 border border-slate-700/30">
                <span class="text-[10px] font-bold tracking-widest text-indigo-400 uppercase">Materi Pembelajaran</span>
                <h2 class="font-outfit font-extrabold text-2xl md:text-3xl text-white mt-1 mb-6 leading-tight">{{ $currentLesson->title }}</h2>
                
                <div class="prose prose-invert prose-indigo max-w-none text-slate-300 text-sm md:text-base leading-relaxed space-y-4">
                    @if($currentLesson->content_text)
                        {!! $currentLesson->content_text !!}
                    @else
                        <p class="text-slate-500 italic">Materi tulisan kosong. Silakan tonton video yang dilampirkan.</p>
                    @endif
                </div>
            </div>

            <!-- Previous / Next Navigation -->
            @php
                $allLessons = collect();
                foreach($course->modules as $mod) {
                    foreach($mod->lessons as $les) {
                        $allLessons->push($les);
                    }
                }
                $currentIndex = $allLessons->search(fn($les) => $les->id == $currentLesson->id);
                $prevLesson = $currentIndex > 0 ? $allLessons->get($currentIndex - 1) : null;
                $nextLesson = $currentIndex < $allLessons->count() - 1 ? $allLessons->get($currentIndex + 1) : null;
            @endphp

            <div class="flex items-center justify-between mt-8">
                @if($prevLesson)
                    <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $prevLesson->id]) }}" 
                       class="text-xs font-semibold px-4 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 transition duration-300 text-indigo-400 flex items-center gap-2 border border-slate-700/50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Materi Sebelumnya</span>
                    </a>
                @else
                    <div></div>
                @endif

                @if($nextLesson)
                    <a href="{{ route('lessons.show', ['course' => $course->id, 'lesson' => $nextLesson->id]) }}" 
                       class="text-xs font-semibold px-4 py-3 rounded-xl bg-indigo-500 hover:bg-indigo-600 transition duration-300 text-white flex items-center gap-2 shadow-lg shadow-indigo-500/10">
                        <span>Materi Selanjutnya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @endif
            </div>

        </main>

    </div>

</body>
</html>
