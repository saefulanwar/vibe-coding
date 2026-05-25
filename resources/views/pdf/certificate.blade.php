<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat {{ $user_name }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-white">

    <div class="relative w-[297mm] h-[210mm] overflow-hidden bg-cover bg-center flex flex-col justify-between p-16" 
         style="background-image: url('{{ !empty($background_image_base64) ? $background_image_base64 : asset('storage/' . $template->background_image) }}');">
        
        <div class="text-center mt-12">
            <p class="text-sm tracking-widest text-slate-500 uppercase font-semibold">Sertifikat Kelulusan</p>
            <p class="text-xs text-slate-400 mt-1">Nomor: CERT-{{ $uuid }}</p>
        </div>

        <div class="text-center flex-grow flex flex-col justify-center px-20">
            <p class="text-base text-slate-600">Diberikan Kepada:</p>
            
            <h1 class="text-3xl font-extrabold text-slate-900 my-4 tracking-tight leading-tight">
                {{ $user_name }}
            </h1>
            
            <p class="text-sm text-slate-600 max-w-2xl mx-auto leading-relaxed">
                Atas kelulusannya pada kelas kompetensi <span class="font-bold text-slate-900">"{{ $course_title }}"</span> 
                yang diselenggarakan oleh <span class="font-semibold text-slate-800">{{ $unit_name }}</span> 
                pada platform Glacier LMS.
            </p>
        </div>

        <div class="flex justify-between items-end px-12 pb-6">
            <div class="text-left">
                <div class="p-2 bg-white border border-slate-100 rounded-lg inline-block shadow-xs">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=65x65&data={{ url('/verify/'.$uuid) }}" alt="Verify">
                </div>
                <p class="text-[9px] text-slate-400 mt-1">Pindai untuk verifikasi keaslian</p>
            </div>

            <div class="text-center relative min-w-[200px]">
                <p class="text-xs text-slate-500 mb-14">Yogyakarta, {{ now()->translatedFormat('d F Y') }}</p>
                
                <div class="absolute left-1/2 top-4 -translate-x-1/2 text-transparent select-none pointer-events-none">
                    {{ $template->tag_koordinat }}
                </div>

                <div class="border-t border-slate-300 pt-1.5">
                    <p class="text-xs font-bold text-slate-900">{{ $signer_name }}</p>
                    <p class="text-[10px] text-slate-400">{{ $signer_position }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
