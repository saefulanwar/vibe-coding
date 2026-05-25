<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Sertifikat | Glacier</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-emerald-500 p-6 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white mb-4">
                <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Sertifikat Valid</h1>
            <p class="text-emerald-50 mt-1 text-sm">Sertifikat ini resmi diterbitkan oleh sistem Glacier.</p>
        </div>

        <div class="p-8">
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Diberikan Kepada</h3>
                    <p class="mt-1 text-xl font-bold text-slate-900">{{ $certificate->student_name_snapshot }}</p>
                </div>
                
                <hr class="border-slate-100">

                <div>
                    <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Judul Kompetensi</h3>
                    <p class="mt-1 text-lg text-slate-800 font-medium">{{ $certificate->course_title_snapshot }}</p>
                </div>

                <hr class="border-slate-100">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Tanggal Lulus</h3>
                        <p class="mt-1 text-base text-slate-800">{{ \Carbon\Carbon::parse($certificate->completed_at)->translatedFormat('d F Y') }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-slate-500 uppercase tracking-wider">Nomor Sertifikat</h3>
                        <p class="mt-1 text-base font-mono text-slate-600 text-sm break-all">CERT-{{ explode('-', $certificate->id)[0] }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 flex justify-center">
                <a href="{{ asset('storage/' . $certificate->file_path) }}" target="_blank" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Unduh Dokumen Asli
                </a>
            </div>
        </div>
        
        <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
            <p class="text-xs text-slate-500">Sertifikat ini diamankan dengan Tanda Tangan Elektronik tersertifikasi.</p>
        </div>
    </div>

</body>
</html>
