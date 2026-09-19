<?php

namespace App\Http\Controllers;

use App\Models\CandidateLead;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class CandidateLeadPdfController extends Controller
{
    public function show(CandidateLead $candidateLead)
    {
        $candidateLead->load(['destinationCountry', 'jobCategory']);

        $photoBase64 = $this->imageToBase64($candidateLead->photo_path);
        $passportBase64 = $this->imageToBase64($candidateLead->passport_copy_path);

        $html = view('pdf.candidate-lead', [
            'candidate' => $candidateLead,
            'photoBase64' => $photoBase64,
            'passportBase64' => $passportBase64,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 12,
            'margin_bottom' => 12,
            'margin_left' => 14,
            'margin_right' => 14,
        ]);

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output('Candidate-' . str($candidateLead->name)->slug() . '.pdf', Destination::INLINE),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    private function imageToBase64(?string $path): ?string
    {
        if (! $path || ! Storage::disk('r2')->exists($path)) {
            return null;
        }

        $mime = Storage::disk('r2')->mimeType($path);

        // শুধু ছবি হলেই PDF-এ ইনলাইন বসবে; PDF ফাইল হলে বাদ যাবে
        if (! str_starts_with($mime, 'image/')) {
            return null;
        }

        $imageData = Storage::disk('r2')->get($path);

        return 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }
}