<nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-slate-200" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 bg-sky-600 rounded-lg flex items-center justify-center shadow-sm group-hover:bg-sky-700 transition flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    
                    <div class="flex flex-col items-start justify-center">
                        <span class="font-bold text-lg text-slate-900 tracking-tight leading-none">
                            <span class="tracking-[0.15em]">GLACIER</span>
                        </span>
                        <span class="text-[9px] text-sky-600 font-semibold tracking-wider uppercase mt-1 leading-none">
                            Global Access for Independent Learning
                        </span>
                    </div>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden sm:flex sm:items-center sm:space-x-8">
                <a href="#" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-sm font-medium transition">Beranda</a>
                <a href="#courses" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-sm font-medium transition">Katalog Kursus</a>
                <!-- <a href="#faculties" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-sm font-medium transition">Fakultas & Unit</a> -->
            </div>

            <!-- CTA Button -->
            <div class="hidden sm:flex sm:items-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 transition">
                        Ke Dashboard, {{ explode(' ', Auth::user()->name)[0] }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition mr-3">
                        Halaman Login
                    </a>
                    <a href="{{ route('sso.google.login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 transition">
                        Daftar Gratis
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500">
                    <span class="sr-only">Open main menu</span>
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" x-cloak class="sm:hidden border-b border-slate-200 bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <a href="#" class="bg-sky-50 border-sky-500 text-sky-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Beranda</a>
            <a href="#courses" class="border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition">Katalog Kursus</a>
            <a href="#faculties" class="border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition">Fakultas & Unit</a>
        </div>
        <div class="pt-4 pb-3 border-t border-slate-200">
            <div class="flex items-center px-4">
                @auth
                    <div class="ml-3">
                        <div class="text-base font-medium text-slate-800">{{ Auth::user()->name }}</div>
                        <div class="text-sm font-medium text-slate-500">{{ Auth::user()->email }}</div>
                    </div>
                @else
                    <div class="flex flex-col space-y-2 w-full">
                        <a href="{{ route('login') }}" class="w-full text-center px-4 py-2 border border-slate-300 rounded-md shadow-sm text-base font-medium text-slate-700 bg-white hover:bg-slate-50">
                            Halaman Login
                        </a>
                        <a href="{{ route('sso.google.login') }}" class="w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-sky-600 hover:bg-sky-700">
                            Daftar Gratis
                        </a>
                    </div>
                @endauth
            </div>
            @auth
            <div class="mt-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">Ke Dashboard</a>
            </div>
            @endauth
        </div>
    </div>
</nav>
