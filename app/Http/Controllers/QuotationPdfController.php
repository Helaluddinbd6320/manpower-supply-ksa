<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;

class QuotationPdfController extends Controller
{
    public function show(Quotation $quotation)
    {
        $quotation->load('items.jobCategory');

        $headerImageBase64 = $this->toBase64($quotation->header_image_path);
        $sealImageBase64 = $this->toBase64($quotation->seal_image_path);

        $html = view('pdf.quotation', [
            'quotation' => $quotation,
            'headerImageBase64' => $headerImageBase64,
            'sealImageBase64' => $sealImageBase64,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 8,
            'margin_bottom' => 12,
            'margin_left' => 12,
            'margin_right' => 12,
        ]);

        $mpdf->WriteHTML($html);

        return response(
            $mpdf->Output($quotation->quotation_number . '.pdf', Destination::INLINE),
            200,
            ['Content-Type' => 'application/pdf']
        );
    }

    private function toBase64(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $imageData = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);

        return 'data:' . $mime . ';base64,' . base64_encode($imageData);
    }
}