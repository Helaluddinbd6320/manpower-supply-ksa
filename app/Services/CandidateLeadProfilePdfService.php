<?php

namespace App\Services;

use App\Models\CandidateLead;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class CandidateLeadProfilePdfService
{
    public function generate(Collection $candidates, bool $includePhone = true): string
    {
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 14,
            'margin_right' => 14,
        ]);

        $css = view('pdf.partials.candidate-css')->render();
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

        foreach ($candidates as $index => $candidate) {
            $candidate->loadMissing(['destinationCountry', 'jobCategory', 'followUps']);

            $html = view('pdf.partials.candidate-single', [
                'candidate' => $candidate,
                'photoBase64' => $this->imageToBase64($candidate->photo_path),
                'passportBase64' => $this->imageToBase64($candidate->passport_copy_path),
                'includePhone' => $includePhone,
            ])->render();

            if ($index > 0) {
                $mpdf->AddPage();
            }

            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        }

        $path = storage_path('app/temp-candidate-profiles-' . uniqid() . '.pdf');
        $mpdf->Output($path, \Mpdf\Output\Destination::FILE);

        return $path;
    }

    private function imageToBase64(?string $path): ?string
    {
        if (! $path || ! Storage::disk('r2')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('r2')->mimeType($path);

        if (! str_starts_with($mime, 'image/')) {
            return null;
        }

        $imageData = Storage::disk('r2')->get($path);

        return 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }
}