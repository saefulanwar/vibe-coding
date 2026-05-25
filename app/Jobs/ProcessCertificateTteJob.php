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
        try {
            $this->certificate->update(['status' => 'processing']);

            // Decrypt passphrase
            $passphrase = Crypt::decryptString($this->encryptedPassphrase);

            $user = $this->certificate->user;
            $course = $this->certificate->course;
            $unit = $this->certificate->unit;
            $template = $this->certificate->template;

            if (!$template) {
                throw new \Exception("Certificate template not found.");
            }

            // Encode background image as base64 to ensure 100% reliable local asset loading in headless browser.
            // Check Spatie Media Library first, then fall back to public/private disks.
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

            // Prepare data for HTML
            $data = [
                'user_name' => $this->certificate->student_name_snapshot ?? $user->name,
                'course_title' => $this->certificate->course_title_snapshot ?? $course->title,
                'unit_name' => $unit ? $unit->name : 'Unknown Unit',
                'uuid' => $this->certificate->id,
                'template' => $template,
                'signer_name' => 'Admin Unit',
                'signer_position' => 'Kepala Unit',
                'background_image_base64' => $bgBase64,
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

            // Send to SiAgen UNY
            $response = Http::withHeaders([
                'key' => 'Lw_oJ3KQomQnh_eT29Ep9Li3ybDpiPrY',
            ])->timeout(60)->asMultipart()->post('https://siagen.uny.ac.id/tte-rest/pdf', [
                'nik' => $this->nik,
                'passphrase' => $passphrase,
                'file' => fopen($tempPdfPath, 'r'),
            ]);

            if ($response->successful()) {
                $body = $response->body();

                // SiAgen returns HTTP 200 even on errors, with JSON body like {"status":false,"msg":"error nik"}
                // A valid PDF always starts with %PDF
                if (!str_starts_with($body, '%PDF')) {
                    Log::error('SiAgen API returned non-PDF response: ' . $body);
                    $this->certificate->update(['status' => 'failed']);
                    return;
                }

                // Save signed PDF
                $finalPdfPath = 'certificates/' . $this->certificate->id . '.pdf';
                Storage::disk('public')->put($finalPdfPath, $body);

                // Add generated PDF to Spatie Media Library
                $absolutePath = storage_path('app/public/' . $finalPdfPath);
                $this->certificate->clearMediaCollection('certificates');
                $this->certificate->addMedia($absolutePath)
                    ->toMediaCollection('certificates');

                $this->certificate->update([
                    'status' => 'completed',
                    'file_path' => $finalPdfPath,
                ]);
            } else {
                Log::error('SiAgen API Error (HTTP ' . $response->status() . '): ' . $response->body());
                $this->certificate->update(['status' => 'failed']);
            }

            // Cleanup temp file
            if (file_exists($tempPdfPath)) {
                unlink($tempPdfPath);
            }

        } catch (\Exception $e) {
            Log::error('ProcessCertificateTteJob Error: ' . $e->getMessage());
            $this->certificate->update(['status' => 'failed']);
        }
    }
}
