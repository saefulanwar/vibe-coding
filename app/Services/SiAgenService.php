<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SiAgenService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.siagen.base_url', 'https://siagen.uny.ac.id'), '/');
        $this->apiKey = config('services.siagen.api_key', 'Lw_oJ3KQomQnh_eT29Ep9Li3ybDpiPrY');
    }

    /**
     * Request a new document/certificate number from SiAgen.
     */
    public function requestNomorSurat(array $data): array
    {
        $url = $this->baseUrl . '/penomoran-rest/create?scheme=nomor';

        $payload = [
            'ttd_id' => $data['ttd_id'] ?? config('services.siagen.ttd_id', 1),
            'keamanan_id' => $data['keamanan_id'] ?? config('services.siagen.keamanan_id', 4),
            'kodesuratid' => $data['kodesuratid'] ?? config('services.siagen.kodesuratid', 613),
            'create_at' => $data['create_at'] ?? now()->format('Y-m-d'),
            'hal' => $data['hal'] ?? 'Sertifikat Kelulusan Kursus',
            'jenis_surat_id' => $data['jenis_surat_id'] ?? config('services.siagen.jenis_surat_id', 27),
        ];

        Log::info("SiAgen: Requesting document number with payload", $payload);

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->timeout(60)->asMultipart()->post($url, $payload);

        if ($response->successful()) {
            $body = $response->json();
            Log::info("SiAgen: Penomoran response received", $body ?? ['raw' => $response->body()]);

            if (isset($body['status']) && $body['status'] === true) {
                return [
                    'status' => true,
                    'id' => $body['id'],
                    'nomor' => $body['nomor'],
                    'message' => $body['message'] ?? 'success',
                ];
            }

            throw new Exception('SiAgen Penomoran failed: ' . ($body['message'] ?? $response->body()));
        }

        throw new Exception('SiAgen Penomoran HTTP error: ' . $response->status() . ' - ' . $response->body());
    }

    /**
     * Upload the certificate file to SiAgen.
     */
    public function uploadFileSurat(string $siagenId, string $nomorSurat, string $filePath): bool
    {
        $url = $this->baseUrl . '/penomoran-rest/upload';

        if (!file_exists($filePath)) {
            throw new Exception("File to upload does not exist at path: " . $filePath);
        }

        Log::info("SiAgen: Uploading file for ID {$siagenId}, Nomor: {$nomorSurat}");

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->timeout(60)
        ->attach('file', fopen($filePath, 'r'), 'certificate.pdf', [
            'Content-Type' => 'application/pdf'
        ])
        ->post($url, [
            'id' => $siagenId,
            'nomor_surat' => $nomorSurat,
        ]);

        if ($response->successful()) {
            $body = $response->json();
            Log::info("SiAgen: Upload response received", $body ?? ['raw' => $response->body()]);

            if (isset($body['status']) && $body['status'] === true) {
                return $body['url'] ?? true;
            }

            throw new Exception('SiAgen Upload failed: ' . ($body['message'] ?? $body['massage'] ?? $response->body()));
        }

        throw new Exception('SiAgen Upload HTTP error: ' . $response->status() . ' - ' . $response->body());
    }

    /**
     * Execute digital signature (TTE) for the uploaded document.
     */
    public function executeTte(string $siagenId, string $nomorSurat, string $email, string $passphrase, ?string $nik = null): array
    {
        $url = $this->baseUrl . '/tte-rest/nomor';

        $payload = [
            'id' => $siagenId,
            'nomor_surat' => $nomorSurat,
            'email' => $email,
            'passphrase' => $passphrase,
        ];

        // Only include NIK in non-production/local environments
        if (config('app.env') !== 'production' && !empty($nik)) {
            $payload['nik'] = $nik;
        }

        Log::info("SiAgen: Executing TTE on ID {$siagenId}, Nomor: {$nomorSurat}");

        $response = Http::withHeaders([
            'key' => $this->apiKey,
        ])->timeout(60)->asMultipart()->post($url, $payload);

        if ($response->successful()) {
            $body = $response->json();
            Log::info("SiAgen: TTE response received", $body ?? ['raw' => $response->body()]);

            if (isset($body['status']) && $body['status'] === true) {
                return [
                    'status' => true,
                    'message' => $body['message'] ?? $body['massage'] ?? 'success',
                ];
            }

            return [
                'status' => false,
                'message' => $body['message'] ?? $body['massage'] ?? $response->body(),
            ];
        }

        throw new Exception('SiAgen TTE HTTP error: ' . $response->status() . ' - ' . $response->body());
    }
}
