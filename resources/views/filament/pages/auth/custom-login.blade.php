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
        font-size: 120px;
        font-weight: 900;
        color: rgba(245, 48, 3, 0.2);
        letter-spacing: -0.05em;
        user-select: none;
    }
    
    .dark .glacier-letter-g {
        color: rgba(246, 21, 0, 0.2);
    }
</style>

<div class="glacier-login-container">
    
    <!-- Top Header -->
    <header class="glacier-header">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="glacier-logo-group">
            <div class="glacier-logo-box">
                <svg style="width: 18px; height: 18px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="glacier-logo-text">GLACIER</span>
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
            {{-- Laravel Logo background graphic --}}
            <svg class="glacier-svg-bg" viewBox="0 0 438 104" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.2036 -3H0V102.197H49.5189V86.7187H17.2036V-3Z" fill="currentColor" />
                <path d="M110.256 41.6337C108.061 38.1275 104.945 35.3731 100.905 33.3681C96.8667 31.3647 92.8016 30.3618 88.7131 30.3618C83.4247 30.3618 78.5885 31.3389 74.201 33.2923C69.8111 35.2456 66.0474 37.928 62.9059 41.3333C59.7643 44.7401 57.3198 48.6726 55.5754 53.1293C53.8287 57.589 52.9572 62.274 52.9572 67.1813C52.9572 72.1925 53.8287 76.8995 55.5754 81.3069C57.3191 85.7173 59.7636 89.6241 62.9059 93.0293C66.0474 96.4361 69.8119 99.1155 74.201 101.069C78.5885 103.022 83.4247 103.999 88.7131 103.999C92.8016 103.999 96.8667 102.997 100.905 100.994C104.945 98.9911 108.061 96.2359 110.256 92.7282V102.195H126.563V32.1642H110.256V41.6337ZM108.76 75.7472C107.762 78.4531 106.366 80.8078 104.572 82.8112C102.776 84.8161 100.606 86.4183 98.0637 87.6206C95.5202 88.823 92.7004 89.4238 89.6103 89.4238C86.5178 89.4238 83.7252 88.823 81.2324 87.6206C78.7388 86.4183 76.5949 84.8161 74.7998 82.8112C73.004 80.8078 71.6319 78.4531 70.6856 75.7472C69.7356 73.0421 69.2644 70.1868 69.2644 67.1821C69.2644 64.1758 69.7356 61.3205 70.6856 58.6154C71.6319 55.9102 73.004 53.5571 74.7998 51.5522C76.5949 49.5495 78.738 47.9451 81.2324 46.7427C83.7252 45.5404 86.5178 44.9396 89.6103 44.9396C92.7012 44.9396 95.5202 45.5404 98.0637 46.7427C100.606 47.9451 102.776 49.5487 104.572 51.5522C106.367 53.5571 107.762 55.9102 108.76 58.6154C109.756 61.3205 110.256 64.1758 110.256 67.1821C110.256 70.1868 109.756 73.0421 108.76 75.7472Z" fill="currentColor" />
                <path d="M242.805 41.6337C240.611 38.1275 237.494 35.3731 233.455 33.3681C229.416 31.3647 225.351 30.3618 221.262 30.3618C215.974 30.3618 211.138 31.3389 206.75 33.2923C202.36 35.2456 198.597 37.928 195.455 41.3333C192.314 44.7401 189.869 48.6726 188.125 53.1293C186.378 57.589 185.507 62.274 185.507 67.1813C185.507 72.1925 186.378 76.8995 188.125 81.3069C189.868 85.7173 192.313 89.6241 195.455 93.0293C198.597 96.4361 202.361 99.1155 206.75 101.069C211.138 103.022 215.974 103.999 221.262 103.999C225.351 103.999 229.416 102.997 233.455 100.994C237.494 98.9911 240.611 96.2359 242.805 92.7282V102.195H259.112V32.1642H242.805V41.6337ZM241.31 75.7472C240.312 78.4531 238.916 80.8078 237.122 82.8112C235.326 84.8161 233.156 86.4183 230.614 87.6206C228.07 88.823 225.251 89.4238 222.16 89.4238C219.068 89.4238 216.275 88.823 213.782 87.6206C211.289 86.4183 209.145 84.8161 207.35 82.8112C205.554 80.8078 204.182 78.4531 203.236 75.7472C202.286 73.0421 201.814 70.1868 201.814 67.1821C201.814 64.1758 202.286 61.3205 203.236 58.6154C204.182 55.9102 205.554 53.5571 207.35 51.5522C209.145 49.5495 211.288 47.9451 213.782 46.7427C216.275 45.5404 219.068 44.9396 222.16 44.9396C225.251 44.9396 228.07 45.5404 230.614 46.7427C233.156 47.9451 235.326 49.5487 237.122 51.5522C238.917 53.5571 240.312 55.9102 241.31 58.6154C242.306 61.3205 242.806 64.1758 242.806 67.1821C242.805 70.1868 242.305 73.0421 241.31 75.7472Z" fill="currentColor" />
                <path d="M438 -3H421.694V102.197H438V-3Z" fill="currentColor" />
                <path d="M139.43 102.197H155.735V48.2834H183.712V32.1665H139.43V102.197Z" fill="currentColor" />
                <path d="M324.49 32.1665L303.995 85.794L283.498 32.1665H266.983L293.748 102.197H314.242L341.006 32.1665H324.49Z" fill="currentColor" />
                <path d="M376.571 30.3656C356.603 30.3656 340.797 46.8497 340.797 67.1828C340.797 89.6597 356.094 104 378.661 104C391.29 104 399.354 99.1488 409.206 88.5848L398.189 80.0226C398.183 80.031 389.874 90.9895 377.468 90.9895C363.048 90.9895 356.977 79.3111 356.977 73.269H411.075C413.917 50.1328 398.775 30.3656 376.571 30.3656ZM357.02 61.0967C357.145 59.7487 359.023 43.3761 376.442 43.3761C393.861 43.3761 395.978 59.7464 396.099 61.0967H357.02Z" fill="currentColor" />
            </svg>

            {{-- Glacier branding overlay decoration --}}
            <div class="glacier-overlay-g">
                <span class="glacier-letter-g">
                    G
                </span>
            </div>
        </div>
    </div>
</div>
