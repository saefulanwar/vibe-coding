<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\Enrollment;
use App\Services\PaymentService;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected PaymentService $paymentService;
    protected EnrollmentService $enrollmentService;

    public function __construct(PaymentService $paymentService, EnrollmentService $enrollmentService)
    {
        $this->paymentService = $paymentService;
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Process direct course checkout
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk membeli kursus.');
        }

        $user = Auth::user();
        $course = Course::findOrFail($request->course_id);

        // Check active enrollment
        $activeEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($activeEnrollment) {
            return redirect()->route('dashboard')->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Create transaction reference number
        $referenceNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Create draft Order
        $order = Order::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
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
}
