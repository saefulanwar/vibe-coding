<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Exception;

class ProcessCertificateTteJob implements ShouldQueue
{
    use Queueable;

    public Certificate $certificate;
    public string $nik;
    public string $encryptedPassphrase;

    /**
     * Create a new job instance.
     */
    public function __construct(Certificate $certificate, string $nik, string $encryptedPassphrase)
    {
        $this->certificate = $certificate;
        $this->nik = $nik;
        $this->encryptedPassphrase = $encryptedPassphrase;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $tempPdfPath = null;
        try {
            $this->certificate->update(['status' => 'processing']);

            // Decrypt passphrase
            $passphrase = Crypt::decryptString($this->encryptedPassphrase);

            $user = $this->certificate->user;
            $course = $this->certificate->course;
            $unit = $this->certificate->unit;
            $template = $this->certificate->template;

            if (!$template) {
                throw new Exception("Certificate template not found.");
            }

            // Encode background image as base64 to ensure 100% reliable local asset loading in headless browser.
            $bgPath = '';
            if ($template->hasMedia('background_image')) {
                $bgPath = $template->getFirstMediaPath('background_image');
            } else {
                $bgPublicPath = storage_path('app/public/' . $template->background_image);
                $bgPrivatePath = storage_path('app/private/' . $template->background_image);
                if (!empty($template->background_image)) {
                    if (file_exists($bgPublicPath)) {
                        $bgPath = $bgPublicPath;
                    } elseif (file_exists($bgPrivatePath)) {
                        $bgPath = $bgPrivatePath;
                    }
                }
            }

            $bgBase64 = '';
            if (!empty($bgPath) && file_exists($bgPath)) {
                $bgData = file_get_contents($bgPath);
                $extension = strtolower(pathinfo($bgPath, PATHINFO_EXTENSION));
                $mimeType = 'image/jpeg';
                if ($extension === 'png') {
                    $mimeType = 'image/png';
                } elseif ($extension === 'svg') {
                    $mimeType = 'image/svg+xml';
                } elseif (function_exists('mime_content_type')) {
                    $mimeType = @mime_content_type($bgPath) ?: 'image/jpeg';
                }
                $bgBase64 = 'data:' . $mimeType . ';base64,' . base64_encode($bgData);
            }

            // Initialize SiAgen service
            $siagenService = app(\App\Services\SiAgenService::class);

            // Step 1: Minta Nomor Surat
            Log::info("ProcessCertificateTteJob: Requesting document number for certificate: " . $this->certificate->id);
            try {
                $nomorData = $siagenService->requestNomorSurat([
                    'hal' => 'Sertifikat Kelulusan: ' . ($this->certificate->course_title_snapshot ?? $course->title),
                ]);

                if (isset($nomorData['status']) && $nomorData['status'] === true) {
                    $this->certificate->update([
                        'siagen_id' => $nomorData['id'],
                        'siagen_nomor' => $nomorData['nomor']
                    ]);
                    Log::info("ProcessCertificateTteJob: Assigned SiAgen ID {$nomorData['id']} and Nomor {$nomorData['nomor']}");
                } else {
                    throw new Exception("SiAgen numbering returned false status.");
                }
            } catch (Exception $e) {
                Log::error("ProcessCertificateTteJob: Failed to request SiAgen number: " . $e->getMessage());

                // Local fallback for numbering
                if (config('app.env') === 'local') {
                    Log::warning("Local environment fallback: Generating fake numbering for testing.");
                    $this->certificate->update([
                        'siagen_id' => 'fake_' . rand(10000, 99999),
                        'siagen_nomor' => 'CERT/UNY/' . now()->format('Y') . '/' . rand(100, 999),
                    ]);
                } else {
                    throw $e;
                }
            }

            // Step 2: Generate PDF dengan Nomor Surat
            $data = [
                'user_name' => $this->certificate->student_name_snapshot ?? $user->name,
                'course_title' => $this->certificate->course_title_snapshot ?? $course->title,
                'unit_name' => $unit ? $unit->name : 'Unknown Unit',
                'uuid' => $this->certificate->id,
                'template' => $template,
                'signer_name' => 'Admin Unit',
                'signer_position' => 'Kepala Unit',
                'background_image_base64' => $bgBase64,
                'siagen_nomor' => $this->certificate->siagen_nomor,
            ];

            // Render HTML to PDF Mentah
            $html = View::make('pdf.certificate', $data)->render();
            $tempPdfPath = storage_path('app/temp/cert_' . Str::random(10) . '.pdf');
            
            $tempDir = storage_path('app/temp');
            if (!is_dir($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            Browsershot::html($html)
                ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
                ->addChromiumArguments(['--no-sandbox', '--disable-setuid-sandbox'])
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->format('A4')
                ->landscape()
                ->save($tempPdfPath);

            $uploadSuccess = false;
            $uploadUrl = null;

            // Step 3: Upload File Surat ke SiAgen
            if (!str_starts_with($this->certificate->siagen_id, 'fake_')) {
                Log::info("ProcessCertificateTteJob: Uploading PDF to SiAgen for ID " . $this->certificate->siagen_id);
                try {
                    $uploadResult = $siagenService->uploadFileSurat(
                        $this->certificate->siagen_id,
                        $this->certificate->siagen_nomor,
                        $tempPdfPath
                    );

                    if (is_string($uploadResult)) {
                        $uploadUrl = $uploadResult;
                        $uploadSuccess = true;
                    } elseif ($uploadResult === true) {
                        $uploadSuccess = true;
                    }
                } catch (Exception $e) {
                    Log::error("ProcessCertificateTteJob: Upload to SiAgen failed: " . $e->getMessage());
                }
            }

            // Step 4: Eksekusi TTE
            $tteSuccess = false;
            if ($uploadSuccess) {
                Log::info("ProcessCertificateTteJob: Requesting TTE signature execution from SiAgen.");
                try {
                    $signerEmail = config('services.siagen.signer_email', 'dummy@dummy.com');
                    $tteResult = $siagenService->executeTte(
                        $this->certificate->siagen_id,
                        $this->certificate->siagen_nomor,
                        $signerEmail,
                        $passphrase,
                        $this->nik
                    );

                    if (isset($tteResult['status']) && $tteResult['status'] === true) {
                        Log::info("ProcessCertificateTteJob: TTE successfully executed.");
                        $tteSuccess = true;
                    } else {
                        Log::error("ProcessCertificateTteJob: TTE execution failed: " . ($tteResult['message'] ?? 'Unknown error'));
                    }
                } catch (Exception $e) {
                    Log::error("ProcessCertificateTteJob: TTE execution exception: " . $e->getMessage());
                }
            }

            // Step 5: Save PDF and Complete
            $finalPdfPath = 'certificates/' . $this->certificate->id . '.pdf';
            $absolutePath = storage_path('app/public/' . $finalPdfPath);

            if ($tteSuccess && !empty($uploadUrl)) {
                // Download the signed PDF from the gateway URL
                Log::info("ProcessCertificateTteJob: Downloading signed PDF from Gateway URL: " . $uploadUrl);
                try {
                    $downloadResponse = Http::timeout(60)->get($uploadUrl);
                    if ($downloadResponse->successful() && str_starts_with($downloadResponse->body(), '%PDF')) {
                        Storage::disk('public')->put($finalPdfPath, $downloadResponse->body());

                        // Add signed PDF to Spatie Media Library
                        $this->certificate->clearMediaCollection('certificates');
                        $this->certificate->addMedia($absolutePath)->toMediaCollection('certificates');

                        $this->certificate->update([
                            'status' => 'completed',
                            'file_path' => $finalPdfPath,
                        ]);

                        Log::info("ProcessCertificateTteJob: Finished successfully with signed PDF.");
                        
                        // Cleanup
                        if (file_exists($tempPdfPath)) {
                            unlink($tempPdfPath);
                        }
                        return;
                    } else {
                        Log::error("ProcessCertificateTteJob: Gateway download did not return a valid PDF.");
                    }
                } catch (Exception $e) {
                    Log::error("ProcessCertificateTteJob: Failed to download signed PDF: " . $e->getMessage());
                }
            }

            // Fallback for Local or Failed Signature
            if (config('app.env') === 'local') {
                Log::warning("Local environment fallback: Falling back to local unsigned PDF certificate.");
                Storage::disk('public')->put($finalPdfPath, file_get_contents($tempPdfPath));

                // Add to Media Library
                $this->certificate->clearMediaCollection('certificates');
                $this->certificate->addMedia($absolutePath)->toMediaCollection('certificates');

                $this->certificate->update([
                    'status' => 'completed',
                    'file_path' => $finalPdfPath,
                ]);
            } else {
                $this->certificate->update(['status' => 'failed']);
            }

            // Cleanup temp file
            if (file_exists($tempPdfPath)) {
                unlink($tempPdfPath);
            }

        } catch (Exception $e) {
            Log::error('ProcessCertificateTteJob General Error: ' . $e->getMessage());
            $this->certificate->update(['status' => 'failed']);
            
            // Cleanup temp file on exception
            if (!empty($tempPdfPath) && file_exists($tempPdfPath)) {
                unlink($tempPdfPath);
            }
        }
    }
}
