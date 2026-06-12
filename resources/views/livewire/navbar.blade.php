<nav class="sticky top-0 z-50 backdrop-blur-md bg-white/80 border-b border-slate-200" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
           <div class="flex-shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-14 h-14 bg-transparent flex items-center justify-center transition-transform duration-300 group-hover:scale-105 flex-shrink-0 overflow-hidden relative">
                        
                        <svg class="w-10 h-10 relative z-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 4.5L5.5 16.5H8.5L12 4.5Z" fill="#0284C7" />
                            
                            <path d="M12 4.5L15 16.5H18.5L12 4.5Z" fill="#075985" />
                            
                            <path d="M12 4.5L8.5 16.5H12V4.5Z" fill="#94A3B8" opacity="0.3" />
                            <path d="M12 4.5L8.5 16.5H12V4.5Z" fill="#E2E8F0" />
                            
                            <path d="M12 4.5L12 16.5H15L12 4.5Z" fill="#38BDF8" />
                            
                            <path d="M12 4.5L12 16.5H12.4L12 4.5Z" fill="#FFFFFF" fill-opacity="0.9" />
                            
                            <path d="M6 18H18" stroke="#64748B" stroke-width="0.75" stroke-linecap="round" opacity="0.4" />
                        </svg>
                    </div>
                    
                    <div class="flex flex-col items-start justify-center">
                        <span class="font-bold text-lg text-slate-900 tracking-tight leading-none group-hover:text-sky-600 transition-colors duration-300">
                            <span class="tracking-[0.15em]">GLACIER</span>
                        </span>
                        <span class="text-[9px] text-sky-600 font-semibold tracking-wider uppercase mt-1 leading-none group-hover:text-sky-500 transition-colors duration-300">
                            Global Access for Independent Learning
                        </span>
                    </div>
                </a>
            </div>
            <!-- Desktop Menu -->
            <div class="hidden sm:flex sm:items-center sm:space-x-8">
                <a href="#" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-sm font-medium transition">{{ __('Beranda') }}</a>
                <a href="#courses" class="text-slate-600 hover:text-sky-600 px-3 py-2 rounded-md text-sm font-medium transition">{{ __('Katalog Kursus') }}</a>
            </div>

            <!-- CTA Button & Language Switcher -->
            <div class="hidden sm:flex sm:items-center">
                <!-- Desktop Language Switcher -->
                <div class="relative mr-4" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition">
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <svg class="w-4 h-4 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-32 bg-white border border-slate-200 rounded-lg shadow-lg py-1 z-50">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 transition @if(app()->getLocale() == $localeCode) font-bold text-sky-600 @endif">
                                {{ $properties['native'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 transition">
                        {{ __('Ke Dashboard') }}, {{ explode(' ', Auth::user()->name)[0] }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 rounded-lg shadow-sm text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition mr-3">
                        {{ __('Halaman Login') }}
                    </a>
                    <a href="{{ route('sso.google.login') }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 transition">
                        {{ __('Daftar Gratis') }}
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
        <!-- Language Switcher Mobile (Paling Atas) -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 bg-slate-50/50">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ __('Bahasa') }}</span>
            <div class="flex gap-2">
                @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                    <a rel="alternate" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" class="px-2.5 py-1 rounded text-xs font-semibold transition border @if(app()->getLocale() == $localeCode) bg-sky-600 text-white border-sky-600 @else bg-white text-slate-600 border-slate-200 hover:bg-slate-50 @endif">
                        {{ strtoupper($localeCode) }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <a href="#" class="bg-sky-50 border-sky-500 text-sky-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">{{ __('Beranda') }}</a>
            <a href="#courses" class="border-transparent text-slate-500 hover:bg-slate-50 hover:border-slate-300 hover:text-slate-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition">{{ __('Katalog Kursus') }}</a>
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
                            {{ __('Halaman Login') }}
                        </a>
                        <a href="{{ route('sso.google.login') }}" class="w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-sky-600 hover:bg-sky-700">
                            {{ __('Daftar Gratis') }}
                        </a>
                    </div>
                @endauth
            </div>
            @auth
            <div class="mt-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium text-slate-500 hover:text-slate-800 hover:bg-slate-100">{{ __('Ke Dashboard') }}</a>
            </div>
            @endauth
        </div>
    </div>
</nav>
