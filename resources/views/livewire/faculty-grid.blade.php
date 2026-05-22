<section id="faculties" class="py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-900">Eksplorasi Berdasarkan Fakultas & Unit</h2>
            <p class="mt-4 text-lg text-slate-600">Temukan spesialisasi yang paling sesuai dengan minat Anda dari unit pengelola terbaik kami.</p>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @foreach($units as $unit)
                <button 
                    wire:click="$dispatch('filterByUnit', { unitId: {{ $unit->id }} })"
                    onclick="document.getElementById('courses').scrollIntoView({behavior: 'smooth'})"
                    class="group flex flex-col items-center p-6 bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 w-full"
                >
                    <div class="w-16 h-16 bg-sky-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-sky-100 transition">
                        <span class="text-2xl font-bold text-sky-600">{{ substr($unit->name, 0, 1) }}</span>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-900 text-center group-hover:text-sky-600 transition">{{ $unit->name }}</h3>
                    <p class="text-xs text-slate-500 mt-2">{{ $unit->courses_count }} Kursus</p>
                </button>
            @endforeach
        </div>
    </div>
</section>
