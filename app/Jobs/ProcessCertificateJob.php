<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ProcessCertificateJob implements ShouldQueue
{
    use Queueable;

    public Certificate $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function handle(): void
    {
        try {
            $this->certificate->update(['status' => 'processing']);

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

            $html = View::make('pdf.certificate', $data)->render();
            $finalPdfPath = 'certificates/' . $this->certificate->id . '.pdf';
            $absolutePath = storage_path('app/public/' . $finalPdfPath);
            
            if (!Storage::disk('public')->exists('certificates')) {
                Storage::disk('public')->makeDirectory('certificates');
            }

            Browsershot::html($html)
                ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
                ->addChromiumArguments(['--no-sandbox', '--disable-setuid-sandbox'])
                ->showBackground()
                ->waitUntilNetworkIdle()
                ->format('A4')
                ->landscape()
                ->save($absolutePath);

            // Add generated PDF to Spatie Media Library
            $this->certificate->clearMediaCollection('certificates');
            $this->certificate->addMedia($absolutePath)
                ->toMediaCollection('certificates');

            $this->certificate->update([
                'status' => 'completed',
                'file_path' => $finalPdfPath,
            ]);

        } catch (\Exception $e) {
            Log::error('ProcessCertificateJob Error: ' . $e->getMessage());
            $this->certificate->update(['status' => 'failed']);
            throw $e;
        }
    }
}
