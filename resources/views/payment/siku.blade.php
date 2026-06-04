<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran SIKU UNY - Glacier LMS</title>
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
            background-color: #080c14;
            background-image: 
                radial-gradient(at 50% 0%, hsla(220,70%,15%,0.4) 0, transparent 50%),
                radial-gradient(at 0% 100%, hsla(240,60%,10%,0.3) 0, transparent 50%);
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(13, 20, 35, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .glow-btn {
            box-shadow: 0 0 20px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
        }
        .glow-btn:hover {
            box-shadow: 0 0 30px rgba(79, 70, 229, 0.5);
            transform: translateY(-1px);
        }
        .siku-card-bg {
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="font-jakarta text-slate-300 min-h-screen flex items-center justify-center p-4 md:p-8">

    <div class="w-full max-w-2xl glass-card rounded-3xl overflow-hidden shadow-2xl border border-slate-800/60">
        
        <!-- Header -->
        <div class="p-6 md:p-8 bg-slate-900/90 border-b border-slate-800/80 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-600/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="font-outfit font-bold text-base tracking-wide text-white uppercase">Invoice SIKU UNY</h1>
                    <span class="text-[10px] tracking-wider text-indigo-400 font-semibold font-outfit uppercase">Sistem Keuangan Universitas Negeri Yogyakarta</span>
                </div>
            </div>
            <span class="text-[10px] font-bold text-amber-400 bg-amber-400/10 px-3 py-1.5 rounded-full border border-amber-400/20 uppercase tracking-wider">
                Menunggu Pembayaran
            </span>
        </div>

        <div class="p-6 md:p-8 space-y-6">

            <!-- Flash Session Alerts -->
            @if(session('info'))
                <div class="p-4 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs flex items-center gap-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs flex items-center gap-3">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Siku Virtual Billing Card -->
            <div class="siku-card-bg rounded-2xl p-6 shadow-xl relative overflow-hidden text-white flex flex-col justify-between min-h-[160px]">
                <div class="absolute -right-6 -bottom-6 w-48 h-48 rounded-full bg-indigo-500/10 blur-2xl"></div>
                <div class="flex justify-between items-start z-10">
                    <div>
                        <p class="text-[10px] tracking-widest uppercase opacity-75 font-semibold">Nomor Billing / VA</p>
                        <div class="flex items-center gap-3 mt-1">
                            <h2 id="billing-number" class="font-outfit font-bold text-2xl tracking-wide text-white">{{ $billingNumber }}</h2>
                            <button onclick="copyBillingNumber()" class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 transition text-slate-200" title="Salin nomor tagihan">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <span class="text-[9px] font-bold tracking-widest text-indigo-200 border border-indigo-400/30 px-2 py-1 rounded bg-indigo-950/40">SIKU-SIVA</span>
                </div>
                <div class="z-10 mt-6 md:mt-0 flex justify-between items-end">
                    <div>
                        <p class="text-[10px] tracking-widest uppercase opacity-75 font-semibold">Total Pembayaran</p>
                        <h3 class="font-outfit font-black text-3xl text-emerald-400 mt-1">Rp {{ number_format($order->amount, 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] tracking-widest uppercase opacity-60 font-semibold">Batas Waktu</p>
                        <p class="text-[10px] font-semibold text-slate-300 mt-0.5">{{ now()->addDays(30)->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Transaction Details Table -->
            <div class="bg-slate-950/50 rounded-2xl p-5 border border-slate-800/80 space-y-3.5">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Mitra Pembayar</span>
                    <span class="text-slate-200 font-semibold">{{ $order->user->name }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Email / NIK</span>
                    <span class="text-slate-200 font-semibold text-right">{{ $order->user->email }} / {{ $order->user->nik }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Item Pembelian</span>
                    <span class="text-slate-200 font-semibold text-right max-w-[280px] truncate" title="{{ $order->course->title }}">{{ $order->course->title }}</span>
                </div>
                @if($invoiceUrl)
                    <div class="border-t border-slate-800/60 pt-3 flex justify-between items-center">
                        <span class="text-xs text-slate-400 font-medium">Dokumen Tagihan</span>
                        <a href="{{ $invoiceUrl }}" target="_blank" class="text-xs text-indigo-400 hover:text-indigo-300 font-bold flex items-center gap-1.5 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span>Unduh PDF Invoice Resmi</span>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Payment Instructions (Tabs/Accordions style) -->
            <div class="space-y-3">
                <h4 class="font-outfit font-bold text-xs text-slate-200 uppercase tracking-wider">Instruksi Cara Pembayaran</h4>
                
                <div class="space-y-2 text-xs">
                    <!-- Bank BTN -->
                    <details class="group bg-slate-900/50 rounded-xl border border-slate-800/60 overflow-hidden" open>
                        <summary class="p-3.5 font-semibold text-slate-300 hover:text-white transition cursor-pointer list-none flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                <span>Transfer VA Bank BTN (BTN Mobile / ATM BTN)</span>
                            </span>
                            <svg class="w-4 h-4 transform group-open:rotate-180 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <div class="px-4 pb-4 pt-1 text-slate-400 space-y-2.5 border-t border-slate-950/40">
                            <div>
                                <p class="font-semibold text-slate-200 mb-1">A. Melalui Aplikasi BTN Mobile:</p>
                                <p class="pl-3">1. Login ke aplikasi <strong>BTN Mobile</strong> Anda.</p>
                                <p class="pl-3">2. Pilih menu <strong>Pembayaran</strong> > <strong>Virtual Account</strong>.</p>
                                <p class="pl-3">3. Masukkan nomor Virtual Account / Billing: <strong class="text-slate-200 font-mono">{{ $billingNumber }}</strong>.</p>
                                <p class="pl-3">4. Konfirmasi nama pembayaran dan total tagihan <strong class="text-emerald-400">Rp {{ number_format($order->amount, 0, ',', '.') }}</strong>, lalu masukkan PIN transaksi Anda.</p>
                            </div>
                            <div class="border-t border-slate-800/50 pt-2">
                                <p class="font-semibold text-slate-200 mb-1">B. Melalui ATM Bank BTN:</p>
                                <p class="pl-3">1. Masukkan kartu ATM dan PIN BTN Anda.</p>
                                <p class="pl-3">2. Pilih menu <strong>Transaksi Lainnya</strong> > <strong>Pembayaran</strong> > <strong>Virtual Account</strong>.</p>
                                <p class="pl-3">3. Masukkan nomor Virtual Account / Billing: <strong class="text-slate-200 font-mono">{{ $billingNumber }}</strong>.</p>
                                <p class="pl-3">4. Periksa detail tagihan yang muncul pada layar, lalu tekan <strong>Ya / Benar</strong> untuk menyelesaikan pembayaran.</p>
                            </div>
                        </div>
                    </details>

                    <!-- Bank Lain (Transfer VA / Antar Bank) -->
                    <details class="group bg-slate-900/50 rounded-xl border border-slate-800/60 overflow-hidden">
                        <summary class="p-3.5 font-semibold text-slate-300 hover:text-white transition cursor-pointer list-none flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                <span>Transfer dari Bank Lain (Mandiri, BNI, BRI, BCA, dll.)</span>
                            </span>
                            <svg class="w-4 h-4 transform group-open:rotate-180 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </summary>
                        <div class="px-4 pb-4 pt-1 text-slate-400 space-y-2 border-t border-slate-950/40">
                            <p>1. Buka aplikasi Mobile Banking Bank lain (Livin', BNI Mobile, BRImo, BCA Mobile, dll.).</p>
                            <p>2. Pilih menu <strong>Transfer ke Bank Lain</strong> atau <strong>Transfer Antar Bank</strong>.</p>
                            <p>3. Pilih bank tujuan: <strong>Bank BTN (Bank Tabungan Negara)</strong>.</p>
                            <p>4. Masukkan nomor rekening tujuan dengan nomor Virtual Account: <strong class="text-slate-200 font-mono">{{ $billingNumber }}</strong>.</p>
                            <p>5. Masukkan nominal transfer sebesar <strong class="text-emerald-400">Rp {{ number_format($order->amount, 0, ',', '.') }}</strong>, lalu selesaikan transaksi transfer Anda.</p>
                        </div>
                    </details>
                </div>
            </div>

            <!-- Action Confirm & Return Buttons -->
            <div class="space-y-3 pt-2">
                <form action="{{ route('payment.siku.check', $order->reference_number) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-center text-xs font-bold py-4 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white transition glow-btn flex items-center justify-center gap-2.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>SAYA SUDAH BAYAR / CEK STATUS SEKARANG</span>
                    </button>
                </form>

                <a href="{{ route('dashboard') }}" class="w-full text-center text-xs font-medium py-3 px-4 rounded-xl text-slate-400 hover:text-slate-200 transition duration-300 block">
                    Kembali ke Dashboard
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-900/40 text-center text-[10px] text-slate-500 border-t border-slate-800/80">
            Pembayaran diproses secara aman melalui Integrasi SIKU-SIVA Universitas Negeri Yogyakarta.
        </div>

    </div>

    <script>
        function copyBillingNumber() {
            var billingNum = document.getElementById("billing-number").innerText;
            navigator.clipboard.writeText(billingNum).then(function() {
                alert("Nomor billing berhasil disalin ke clipboard!");
            }, function(err) {
                console.error("Gagal menyalin teks: ", err);
            });
        }
    </script>

</body>
</html>
