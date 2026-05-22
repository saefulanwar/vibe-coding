<div class="min-h-screen flex flex-col bg-slate-50">
    <livewire:navbar />
    
    <main class="flex-grow">
        <livewire:hero-search />
        
        <!-- Fitur/Keunggulan Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-slate-900">Mengapa Belajar Bersama Kami?</h2>
                    <p class="mt-4 text-lg text-slate-600">Platform kursus unggulan yang dirancang untuk kesuksesan karir Anda.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:shadow-lg transition duration-300">
                        <div class="w-12 h-12 bg-sky-100 text-sky-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Sertifikat Resmi</h3>
                        <p class="text-slate-600">Dapatkan sertifikat kompetensi resmi setelah menyelesaikan kursus yang diakui industri.</p>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:shadow-lg transition duration-300">
                        <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Instruktur Ahli Unit</h3>
                        <p class="text-slate-600">Belajar langsung dari para praktisi dan akademisi terbaik di fakultas masing-masing.</p>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 hover:shadow-lg transition duration-300">
                        <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-900 mb-2">Akses Selamanya</h3>
                        <p class="text-slate-600">Sekali mendaftar, Anda bisa mengakses materi kursus kapan saja dan di mana saja tanpa batas.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <livewire:course-catalog />
        
        <!-- Call to Action Section -->
        <section class="bg-gradient-to-r from-blue-900 to-indigo-900 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Siap Untuk Meningkatkan Skil Anda?</h2>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">Bergabunglah dengan ribuan siswa lainnya yang sudah meraih kesuksesan bersama platform kursus GLACIER.</p>
                <a href="{{ route('login') }}" class="inline-block bg-amber-500 hover:bg-amber-600 text-white font-semibold px-8 py-4 rounded-xl transition duration-300 shadow-lg shadow-amber-500/30">
                    Mulai Belajar Sekarang
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 pt-12 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-1 md:col-span-2">
                    <span class="text-2xl font-bold text-white tracking-tight">GLACIER<span class="text-sky-500"> UNY</span></span>
                    <p class="mt-4 text-slate-400 max-w-sm">Platform pembelajaran daring terbaik untuk karir masa depan Anda.</p>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Navigasi</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-white transition">Beranda</a></li>
                        <li><a href="#" class="hover:text-white transition">Katalog Kursus</a></li>
                        <li><a href="#" class="hover:text-white transition">Fakultas & Unit</a></li>
                        <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="#" class="hover:text-white transition">Bantuan / FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-slate-500 text-sm">
                <p>&copy; {{ date('Y') }} GLACIER UNY. Hak Cipta Dilindungi.</p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
</div>
