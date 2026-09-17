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

        $headerImageBase64 = null;

        if ($quotation->header_image_path && Storage::disk('public')->exists($quotation->header_image_path)) {
            $imageData = Storage::disk('public')->get($quotation->header_image_path);
            $mime = Storage::disk('public')->mimeType($quotation->header_image_path);
            $headerImageBase64 = 'data:' . $mime . ';base64,' . base64_encode($imageData);
        }

        $html = view('pdf.quotation', [
            'quotation' => $quotation,
            'headerImageBase64' => $headerImageBase64,
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
}