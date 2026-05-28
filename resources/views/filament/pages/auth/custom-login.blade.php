<div class="glacier-login-container">
    <style>
        .glacier-login-container {
            background-color: #FDFDFC;
            color: #1b1b18;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            width: 100%;
            padding: 24px;
            box-sizing: border-box;
            font-family: inherit;
        }
        
        .dark .glacier-login-container {
            background-color: #0a0a0a;
            color: #EDEDEC;
        }

        .glacier-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1000px;
            margin-bottom: 24px;
            box-sizing: border-box;
        }

        .glacier-logo-group {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .glacier-logo-box {
            width: 32px;
            height: 32px;
            background-color: #0284c7;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .glacier-logo-box:hover {
            background-color: #0369a1;
        }

        .glacier-logo-text {
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #0f172a;
        }
        
        .dark .glacier-logo-text {
            color: #ffffff;
        }

        .glacier-header-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .glacier-home-link {
            color: #475569;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: color 0.2s;
        }

        .glacier-home-link:hover {
            color: #0284c7;
        }
        
        .dark .glacier-home-link {
            color: #94a3b8;
        }
        .dark .glacier-home-link:hover {
            color: #38bdf8;
        }

        /* Dropdown language switcher */
        .glacier-dropdown {
            position: relative;
            display: inline-block;
        }

        .glacier-dropdown-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: #334155;
            background-color: #ffffff;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .dark .glacier-dropdown-btn {
            border-color: #334155;
            color: #cbd5e1;
            background-color: #1e293b;
        }

        .glacier-dropdown-btn:hover {
            background-color: #f8fafc;
        }
        
        .dark .glacier-dropdown-btn:hover {
            background-color: #334155;
        }

        .glacier-dropdown-menu {
            position: absolute;
            right: 0;
            margin-top: 8px;
            width: 128px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            padding: 4px 0;
            z-index: 50;
        }
        
        .dark .glacier-dropdown-menu {
            background-color: #1e293b;
            border-color: #334155;
        }

        .glacier-dropdown-item {
            display: block;
            padding: 8px 16px;
            font-size: 14px;
            color: #334155;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .glacier-dropdown-item:hover {
            background-color: #f1f5f9;
        }
        
        .dark .glacier-dropdown-item {
            color: #cbd5e1;
        }
        .dark .glacier-dropdown-item:hover {
            background-color: #334155;
        }

        .glacier-dropdown-item.active {
            font-weight: 700;
            color: #0284c7;
        }
        .dark .glacier-dropdown-item.active {
            color: #38bdf8;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        /* Main splitscreen card */
        .glacier-card {
            display: flex;
            flex-direction: column-reverse;
            width: 100%;
            max-width: 1000px;
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            box-sizing: border-box;
        }
        
        .dark .glacier-card {
            background-color: #161615;
            border-color: #262626;
        }

        @media (min-width: 1024px) {
            .glacier-card {
                flex-direction: row;
            }
        }

        /* Left column: Login Form */
        .glacier-form-side {
            flex: 1;
            padding: 24px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        @media (min-width: 1024px) {
            .glacier-form-side {
                padding: 48px;
            }
        }

        .glacier-form-wrapper {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }

        .glacier-title-group {
            margin-bottom: 24px;
        }

        .glacier-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px 0;
        }
        
        .dark .glacier-title {
            color: #ffffff;
        }

        .glacier-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }
        
        .dark .glacier-subtitle {
            color: #94a3b8;
        }

        /* Right column: Graphic Side */
        .glacier-graphic-side {
            flex: 1;
            background-color: #fff2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            min-height: 240px;
        }
        
        .dark .glacier-graphic-side {
            background-color: #1D0002;
        }
        
        @media (min-width: 1024px) {
            .glacier-graphic-side {
                min-height: auto;
                width: 440px;
                flex-shrink: 0;
            }
        }

        .glacier-svg-bg {
            width: 100%;
            color: #F53003;
        }
        
        .dark .glacier-svg-bg {
            color: #F61500;
        }

        .glacier-overlay-g {
            position: absolute;
            inset: 0;
            background-color: rgba(255, 242, 242, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .dark .glacier-overlay-g {
            background-color: rgba(29, 0, 2, 0.4);
        }

        .glacier-letter-g {
            font-size: 60px;
            font-weight: 900;
            color: rgba(245, 48, 3, 0.2);
            letter-spacing: -0.05em;
            user-select: none;
        }
        
        .dark .glacier-letter-g {
            color: rgba(246, 21, 0, 0.2);
        }
    </style>
    
    <!-- Top Header -->
    <header class="glacier-header">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="glacier-logo-group flex items-center gap-3 group">
            <div class="glacier-logo-box w-14 h-14 bg-transparent flex items-center justify-center transition-transform duration-300 group-hover:scale-105 flex-shrink-0 overflow-hidden relative">
                        
                <svg class="w-10 h-10 relative z-10" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 4.5L5.5 16.5H8.5L12 4.5Z" fill="#fcfeffff" />
                            
                    <path d="M12 4.5L15 16.5H18.5L12 4.5Z" fill="#fafcfdff" />
                    
                    <path d="M12 4.5L8.5 16.5H12V4.5Z" fill="#94A3B8" opacity="0.3" />
                    <path d="M12 4.5L8.5 16.5H12V4.5Z" fill="#E2E8F0" />
                    
                    <path d="M12 4.5L12 16.5H15L12 4.5Z" fill="#38BDF8" />
                    
                    <path d="M12 4.5L12 16.5H12.4L12 4.5Z" fill="#FFFFFF" fill-opacity="0.9" />
                    
                    <path d="M6 18H18" stroke="#64748B" stroke-width="0.75" stroke-linecap="round" opacity="0.4" />
                </svg>
            </div>
            
                <span class="glacier-logo-text font-bold text-lg text-slate-900 tracking-[0.15em] leading-none group-hover:text-sky-600 transition-colors duration-300">
                    GLACIER
                </span>
        </a>

        <!-- Right Side: Home Link & Language Switcher -->
        <div class="glacier-header-right">
            <a href="{{ route('home') }}" class="glacier-home-link">
                {{ __('Kembali ke Beranda') }}
            </a>
            
            <!-- Language Switcher -->
            <div class="glacier-dropdown" x-data="{ open: false }">
                <button @click="open = !open" type="button" class="glacier-dropdown-btn">
                    <span>{{ strtoupper(app()->getLocale()) }}</span>
                    <svg class="transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px; color: #64748b;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="glacier-dropdown-menu">
                    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                        <a href="{{ route('change-locale', ['locale' => $localeCode]) }}" class="glacier-dropdown-item @if(app()->getLocale() == $localeCode) active @endif">
                            {{ $properties['native'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </header>

    <!-- Main Card split-screen -->
    <div class="glacier-card">
        <!-- Left Side: Login Form -->
        <div class="glacier-form-side">
            <div class="glacier-form-wrapper">
                <div class="glacier-title-group">
                    <h1 class="glacier-title">
                        {{ __('Selamat Datang Kembali') }}
                    </h1>
                    <p class="glacier-subtitle">
                        {{ __('Silakan masuk ke akun Anda untuk mengakses dashboard Glacier.') }}
                    </p>
                </div>

                <!-- Render the simple page component with default headers completely suppressed -->
                <x-filament-panels::page.simple :heading="''" :subheading="''" :logo="false">
                    {{ $this->content }}
                </x-filament-panels::page.simple>
            </div>
        </div>

        <!-- Right Side: Beautiful Landing Page Graphics -->
        <div class="glacier-graphic-side">
            {{-- Glacier Logo background graphic --}}
            <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" width="100%" height="100%">
                <defs>
                    <linearGradient id="ice-light" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#FFFFFF" />
                    <stop offset="100%" stop-color="#E0F2FE" />
                    </linearGradient>

                    <linearGradient id="ice-mid" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#38BDF8" />
                    <stop offset="100%" stop-color="#0EA5E9" />
                    </linearGradient>

                    <linearGradient id="ice-dark" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="#0284C7" />
                    <stop offset="100%" stop-color="#0369A1" />
                    </linearGradient>

                    <linearGradient id="ice-deep" x1="0%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#075985" />
                    <stop offset="100%" stop-color="#0C4A6E" />
                    </linearGradient>

                    <linearGradient id="platinum-glare" x1="100%" y1="0%" x2="0%" y2="100%">
                    <stop offset="0%" stop-color="#FFFFFF" stop-opacity="0.7"/>
                    <stop offset="100%" stop-color="#94A3B8" stop-opacity="0.0"/>
                    </linearGradient>
                </defs>

                <g transform="translate(0, 45)">
                    <polygon points="400,110 220,370 315,370" fill="url(#ice-dark)" />
                    
                    <polygon points="400,110 485,370 580,370" fill="url(#ice-deep)" />

                    <polygon points="400,110 315,370 400,370" fill="url(#ice-light)" />
                    
                    <polygon points="400,110 400,370 485,370" fill="url(#ice-mid)" />
                    
                    <polygon points="400,110 400,370 412,370" fill="url(#platinum-glare)" />
                    
                    <line x1="250" y1="395" x2="550" y2="395" stroke="#1E293B" stroke-width="2" stroke-dasharray="30 15 10 15"/>
                    <line x1="310" y1="405" x2="490" y2="405" stroke="#334155" stroke-width="1.5" stroke-dasharray="8 20"/>
                </g>
            </svg> -->

            {{-- Glacier branding overlay decoration --}}
            <div class="glacier-overlay-g">
                <span class="glacier-letter-g">
                    Global Access For Independent Learning
                </span>
            </div>
        </div>
    </div>
</div>
