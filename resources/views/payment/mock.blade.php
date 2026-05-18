<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gateway Pembayaran (Simulasi) - Hybrid E-Learning</title>
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
                radial-gradient(at 50% 50%, hsla(244,38%,22%,0.25) 0, transparent 60%),
                radial-gradient(at 0% 0%, hsla(253,16%,7%,1) 0, transparent 50%);
            background-attachment: fixed;
        }
        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .card-bg {
            background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);
        }
    </style>
</head>
<body class="font-jakarta text-slate-200 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-xl glass-card rounded-3xl overflow-hidden shadow-2xl border border-slate-700/40">
        
        <!-- Header -->
        <div class="p-6 md:p-8 bg-slate-900/80 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h1 class="font-outfit font-bold text-sm tracking-wide text-white uppercase">Secure Sandbox Checkout</h1>
                    <span class="text-[9px] tracking-widest text-indigo-400 font-semibold font-outfit uppercase">Direct Payment Gateway</span>
                </div>
            </div>
            <span class="text-[10px] font-bold text-amber-400 bg-amber-400/10 px-2.5 py-1 rounded-full border border-amber-400/20">
                Mode Simulasi
            </span>
        </div>

        <div class="p-6 md:p-8 space-y-6">
            
            <!-- Virtual Mock Payment Card -->
            <div class="card-bg rounded-2xl p-6 shadow-xl relative overflow-hidden text-white flex flex-col justify-between h-40">
                <div class="absolute -right-12 -bottom-12 w-44 h-44 rounded-full bg-white/10 blur-xl"></div>
                <div class="flex justify-between items-start z-10">
                    <div>
                        <p class="text-[9px] tracking-widest uppercase opacity-75">Nomer Tagihan</p>
                        <h2 class="font-outfit font-bold text-base tracking-wide">{{ $order->reference_number }}</h2>
                    </div>
                    <svg class="w-8 h-8 opacity-90" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M45 35c0 2.209-1.791 4-4 4H7c-2.209 0-4-1.791-4-4V13c0-2.209 1.791-4 4-4h34c2.209 0 4 1.791 4 4v22z" fill="#2F3E46"/><path d="M35 15h6v4h-6v-4zM3 17h42v4H3v-4z" fill="#FFF"/><path d="M12 29h6v6h-6v-6z" fill="#E9C46A"/></svg>
                </div>
                <div class="z-10">
                    <p class="text-[9px] tracking-widest uppercase opacity-75">Total Pembayaran</p>
                    <h3 class="font-outfit font-black text-2xl">Rp {{ number_format($order->amount, 0, ',', '.') }}</h3>
                </div>
            </div>

            <!-- Transaction Details Table -->
            <div class="bg-slate-950/40 rounded-2xl p-5 border border-slate-800 space-y-3.5">
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Pembeli</span>
                    <span class="text-slate-200 font-semibold">{{ $order->user->name }} ({{ $order->user->email }})</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">Item Pembelian</span>
                    <span class="text-slate-200 font-semibold text-right max-w-[250px] truncate">{{ $order->course->title }}</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-slate-400 font-medium">LMS Platform</span>
                    <span class="text-slate-200 font-semibold capitalize">{{ $order->course->source }} LMS</span>
                </div>
                <div class="border-t border-slate-800 pt-3.5 flex justify-between items-center">
                    <span class="text-xs text-slate-400 font-medium">Total Harga</span>
                    <span class="text-sm font-bold text-emerald-400">Rp {{ number_format($order->amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Action Simulation Buttons -->
            <div class="space-y-3 pt-2">
                <form action="{{ route('payment.complete', $order->reference_number) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="success">
                    <button type="submit" class="w-full text-center text-xs font-bold py-3.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 transition text-slate-950 shadow-lg shadow-emerald-500/10 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        <span>Simulasikan Bayar Berhasil (Settlement)</span>
                    </button>
                </form>

                <form action="{{ route('payment.complete', $order->reference_number) }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="fail">
                    <button type="submit" class="w-full text-center text-xs font-semibold py-3.5 px-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 hover:bg-rose-500 hover:text-white transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Simulasikan Bayar Gagal (Expire / Cancel)</span>
                    </button>
                </form>

                <a href="{{ route('dashboard') }}" class="w-full text-center text-xs font-medium py-3 px-4 rounded-xl text-slate-400 hover:text-slate-200 transition duration-300 block">
                    Batalkan & Kembali
                </a>
            </div>

        </div>

        <!-- Footer -->
        <div class="p-4 bg-slate-900/40 text-center text-[10px] text-slate-500 border-t border-slate-800">
            Sistem pembayaran ini menggunakan enkripsi sandbox TLS 256-bit standar industri.
        </div>

    </div>

</body>
</html>
