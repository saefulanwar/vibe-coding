<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\Order;
use App\Models\Enrollment;
use App\Services\PaymentService;
use App\Services\EnrollmentService;
use App\Services\SikuService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected PaymentService $paymentService;
    protected EnrollmentService $enrollmentService;
    protected SikuService $sikuService;

    public function __construct(PaymentService $paymentService, EnrollmentService $enrollmentService, SikuService $sikuService)
    {
        $this->paymentService = $paymentService;
        $this->enrollmentService = $enrollmentService;
        $this->sikuService = $sikuService;
    }

    /**
     * Process direct course checkout
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'course_batch_id' => 'required|exists:course_batches,id',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk membeli kursus.');
        }

        $user = Auth::user();
        $batch = CourseBatch::findOrFail($request->course_batch_id);
        $course = $batch->course;

        // 1. Cek Batas Waktu Pendaftaran (Time Limit)
        if (now() > $batch->registration_end_date) {
            return redirect()->back()->with('error', 'Waktu pendaftaran angkatan ini telah berakhir.');
        }

        // 2. Cek Kuota Peserta (Capacity Limit)
        $enrollmentsCount = Enrollment::where('course_batch_id', $batch->id)->count();
        if ($enrollmentsCount >= $batch->quota) {
            return redirect()->back()->with('error', 'Kuota angkatan ini sudah penuh.');
        }

        // Check active enrollment in any batch of this course
        $activeEnrollment = Enrollment::where('user_id', $user->id)
            ->whereHas('courseBatch', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($activeEnrollment) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Check if there is an active pending order for this course to prevent multiple VA generation
        $pendingOrder = Order::where('user_id', $user->id)
            ->where('status', 'pending')
            ->whereHas('courseBatch', function ($query) use ($course) {
                $query->where('course_id', $course->id)
                      ->where('registration_end_date', '>', now());
            })
            ->first();

        if ($pendingOrder) {
            session()->flash('info', 'Anda memiliki transaksi pembayaran yang belum diselesaikan untuk kursus ini. Silakan selesaikan pembayaran berikut.');
            if ($pendingOrder->payment_url) {
                return redirect()->away($pendingOrder->payment_url);
            }
            return redirect()->route('payment.siku', ['reference' => $pendingOrder->reference_number]);
        }

        // Create transaction reference number
        $referenceNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Free Course Direct Registration
        if ($course->price == 0) {
            $order = Order::create([
                'user_id' => $user->id,
                'course_batch_id' => $batch->id,
                'reference_number' => $referenceNumber,
                'amount' => 0,
                'status' => 'paid',
                'gateway_response' => [
                    'free' => true,
                    'timestamp' => now()->toIso8601String(),
                ]
            ]);

            // Auto-enroll user in course batch
            $this->enrollmentService->activateOrderAccess($order);

            return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Anda telah terdaftar di kelas gratis ini.');
        }

        // Create draft Order
        $order = Order::create([
            'user_id' => $user->id,
            'course_batch_id' => $batch->id,
            'reference_number' => $referenceNumber,
            'amount' => $course->price,
            'status' => 'pending',
        ]);

        // Request payment url from PaymentService
        $paymentUrl = $this->paymentService->createPaymentUrl($order);

        // Save payment url to order
        $order->update(['payment_url' => $paymentUrl]);

        return redirect()->away($paymentUrl);
    }

    /**
     * Show mock payment simulator page for developer/demo testing
     */
    public function showMockPaymentPage($reference)
    {
        $order = Order::with(['course', 'user'])->where('reference_number', $reference)->firstOrFail();

        return view('payment.mock', compact('order'));
    }

    /**
     * Process mock payment simulation action
     */
    public function completeMockPayment(Request $request, $reference)
    {
        $order = Order::with(['course', 'user'])->where('reference_number', $reference)->firstOrFail();
        $action = $request->input('action'); // 'success' or 'fail'

        if ($action === 'success') {
            $order->update([
                'status' => 'paid',
                'gateway_response' => [
                    'simulation' => true,
                    'status' => 'paid',
                    'timestamp' => now()->toIso8601String(),
                ]
            ]);

            // Auto-enroll user in course
            $this->enrollmentService->activateOrderAccess($order);

            return redirect()->route('dashboard')->with('success', 'Pembayaran Berhasil Disimulasikan! Hak akses Anda telah diaktifkan.');
        } else {
            $order->update([
                'status' => 'failed',
                'gateway_response' => [
                    'simulation' => true,
                    'status' => 'failed',
                    'timestamp' => now()->toIso8601String(),
                ]
            ]);

            return redirect()->route('dashboard')->with('error', 'Pembayaran Disimulasikan GAGAL.');
        }
    }

    /**
     * Show the Siku payment billing details page
     */
    public function showSikuPaymentPage($reference)
    {
        $order = Order::with(['course', 'user'])->where('reference_number', $reference)->firstOrFail();

        // Extract billing number and invoice URL from gateway response
        $billingNumber = $order->gateway_response['siku_billing_number'] ?? null;
        $invoiceUrl = $order->gateway_response['siku_invoice_url'] ?? null;

        return view('payment.siku', compact('order', 'billingNumber', 'invoiceUrl'));
    }

    /**
     * Check current Siku payment status and activate course if paid
     */
    public function checkSikuPaymentStatus(Request $request, $reference)
    {
        $order = Order::with(['course', 'user'])->where('reference_number', $reference)->firstOrFail();
        $billingNumber = $order->gateway_response['siku_billing_number'] ?? null;

        if (!$billingNumber) {
            return redirect()->back()->with('error', 'Nomor billing tagihan tidak ditemukan.');
        }

        try {
            $statusCheck = $this->sikuService->checkPaymentStatus($billingNumber);

            if ($statusCheck['is_paid']) {
                $order->update([
                    'status' => 'paid',
                    'gateway_response' => array_merge($order->gateway_response ?? [], [
                        'status' => 'paid',
                        'checked_at' => now()->toIso8601String(),
                        'siku_status_response' => $statusCheck['raw_response']
                    ])
                ]);

                // Auto-enroll user in course
                $this->enrollmentService->activateOrderAccess($order);

                return redirect()->route('dashboard')->with('success', 'Pembayaran berhasil dikonfirmasi! Anda sekarang dapat mengakses kursus.');
            }

            return redirect()->back()->with('info', 'Tagihan belum dibayar. Silakan lakukan pembayaran terlebih dahulu.');
        } catch (\Exception $e) {
            Log::error('Siku payment status check error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memverifikasi status pembayaran ke sistem Siku: ' . $e->getMessage());
        }
    }
}
