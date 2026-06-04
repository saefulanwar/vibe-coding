<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SikuService
{
    protected string $baseUrl;
    protected string $email;
    protected string $password;
    protected int $appId;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.siku.base_url', 'https://siku.uny.ac.id'), '/');
        $this->email = config('services.siku.email', 'saefulanwar@uny.ac.id');
        $this->password = config('services.siku.password', '123456');
        $this->appId = (int) config('services.siku.app_id', 582931);
    }

    /**
     * Get Siku JWT bearer token (cached for 50 minutes)
     */
    protected function getToken(): string
    {
        $cacheKey = 'siku_jwt_token_' . md5($this->email);

        return Cache::remember($cacheKey, 3000, function () {
            Log::info("Siku: Requesting new JWT login token for {$this->email}");
            
            $response = Http::asForm()->post($this->baseUrl . '/api/login', [
                'email' => $this->email,
                'password' => $this->password,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['access_token'])) {
                    return $body['access_token'];
                }
                if (isset($body['token'])) {
                    return $body['token'];
                }
                throw new Exception('Token key not found in Siku login response: ' . $response->body());
            }

            throw new Exception('Siku authentication failed: ' . $response->status() . ' - ' . $response->body());
        });
    }

    /**
     * Create an order in SIKU billing system.
     * Returns an array with Siku order details and Siva billing code.
     */
    public function createSikuOrder(Order $order): array
    {
        $token = $this->getToken();
        $user = $order->user;
        $course = $order->course;
        $batch = $order->courseBatch;

        // mulai dan akhir, samakan dengan tanggal checkout dan selesai registrasi course
        $mulai = $order->created_at ? $order->created_at->format('Y-m-d') : now()->format('Y-m-d');
        $akhir = ($batch && $batch->registration_end_date) 
            ? $batch->registration_end_date->format('Y-m-d') 
            : now()->addDays(30)->format('Y-m-d');

        // unit isikan dari kode unit user
        $unitCode = $user->unit->code ?? ($course->unit->code ?? config('services.siku.unit', 'FISHIPOL'));

        // keterangan isikan sesuai judul course minimal 4 kata
        $keterangan = $course->title;
        $words = explode(' ', $keterangan);
        if (count($words) < 4) {
            $keterangan = 'Pembelian Kelas ' . $course->title . ' Glacier';
        }
        $keterangan = substr($keterangan, 0, 100);

        $payload = [
            'name' => $user->name,
            'app_id' => $this->appId,
            'mulai' => $mulai,
            'akhir' => $akhir,
            'email' => $user->email,
            'mitra' => 'GLACIER',
            'kategori' => 22,
            'jenis_mitra' => 1,
            'jenis_pendapatan' => $course->ig_id ? (int) $course->ig_id : (int) config('services.siku.jenis_pendapatan', 58),
            'unit' => $unitCode,
            'lokasi' => $course->lokasi_id ? (int) $course->lokasi_id : (int) config('services.siku.lokasi', 309),
            'harga' => (int) $course->price,
            'qty' => 1,
            'satuan' => 'Paket',
            'keterangan' => $keterangan,
            'nik' => $user->nik ?? '1234567890123456',
            'no_hp' => $user->phone_number ?? '082146694545',
            'alamat' => $user->alamat ?? 'Yogyakarta',
        ];

        Log::info("Siku: Posting order for Order Reference: {$order->reference_number}", $payload);

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl . '/api/orders', $payload);

        if ($response->successful()) {
            $body = $response->json();
            Log::info("Siku: Order creation response", $body);

            if (isset($body['api_status']) && $body['api_status'] == 1) {
                $nomor = $body['siva']['response']['nomor'] ?? null;
                $invoiceUrl = $body['siva']['response']['invoice'] ?? null;

                if (!$nomor) {
                    throw new Exception('Billing code (nomor) is missing in Siku Siva response');
                }

                return [
                    'status' => true,
                    'nomor' => $nomor,
                    'invoice_url' => $invoiceUrl,
                    'siku_order_id' => $body['siku']['orders_id'] ?? null,
                    'siku_order_number' => $body['siku']['order_number'] ?? null,
                    'raw_response' => $body,
                ];
            }

            throw new Exception('Siku order generation failed: ' . ($body['api_message'] ?? $response->body()));
        }

        throw new Exception('Siku order HTTP error: ' . $response->status() . ' - ' . $response->body());
    }

    /**
     * Check payment status for a specific Siva billing number.
     * Returns an array containing payment status information.
     */
    public function checkPaymentStatus(string $nomor): array
    {
        $token = $this->getToken();

        Log::info("Siku: Checking payment status for nomor: {$nomor}");

        $response = Http::withToken($token)
            ->acceptJson()
            ->post($this->baseUrl . '/api/cek-status', [
                'app_id' => $this->appId,
                'nomor' => $nomor,
            ]);

        if ($response->successful()) {
            $body = $response->json();
            Log::info("Siku: Payment status response", $body);

            if (isset($body['api_status']) && $body['api_status'] == 1) {
                $paymentData = $body['data'] ?? [];
                
                // Usually status: 0 is unpaid/pending, status: 1 or non-zero is paid.
                $isPaid = isset($paymentData['status']) && $paymentData['status'] == 1;

                return [
                    'status' => true,
                    'is_paid' => $isPaid,
                    'payment_status' => $isPaid ? 'paid' : 'pending',
                    'nominal' => $paymentData['nominal'] ?? null,
                    'invoice_url' => $paymentData['invoice'] ?? null,
                    'receipt_url' => $paymentData['kuitansi'] ?? null,
                    'raw_response' => $body,
                ];
            }

            throw new Exception('Siku payment status check failed: ' . ($body['api_message'] ?? $response->body()));
        }

        throw new Exception('Siku status check HTTP error: ' . $response->status() . ' - ' . $response->body());
    }

    /**
     * Get Instansi/Group (IG) list for a given unit code
     * Returns an associative array of [id => name]
     */
    public function getIgsByUnitCode(string $code): array
    {
        try {
            $token = $this->getToken();

            Log::info("Siku: Requesting IG list for unit code: {$code}");

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->baseUrl . '/api/ig', [
                    'app_id' => $this->appId,
                    'code' => $code,
                ]);

            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['api_status']) && $body['api_status'] == 1 && !empty($body['data'])) {
                    $igs = [];
                    foreach ($body['data'] as $item) {
                        if (isset($item['id']) && isset($item['name'])) {
                            $igs[$item['id']] = $item['name'];
                        }
                    }
                    return $igs;
                }
            }
            
            Log::warning("Siku: Failed to fetch IGs for code {$code}. Body: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Siku IG Exception for code {$code}: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Get Lokasi list for a given unit code
     * Returns an associative array of [id => name]
     */
    public function getLokasisByUnitCode(string $code): array
    {
        try {
            $token = $this->getToken();

            Log::info("Siku: Requesting Lokasi list for unit code: {$code}");

            $response = Http::withToken($token)
                ->acceptJson()
                ->post($this->baseUrl . '/api/lokasi', [
                    'app_id' => $this->appId,
                    'code' => $code,
                ]);

            if ($response->successful()) {
                $body = $response->json();
                if (isset($body['api_status']) && $body['api_status'] == 1 && !empty($body['data'])) {
                    $lokasis = [];
                    foreach ($body['data'] as $item) {
                        if (isset($item['id']) && isset($item['name'])) {
                            $lokasis[$item['id']] = $item['name'];
                        }
                    }
                    return $lokasis;
                }
            }
            
            Log::warning("Siku: Failed to fetch Lokasi list for code {$code}. Body: " . $response->body());
        } catch (\Exception $e) {
            Log::error("Siku Lokasi Exception for code {$code}: " . $e->getMessage());
        }

        return [];
    }
}
