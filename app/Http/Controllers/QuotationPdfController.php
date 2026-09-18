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
        // pcre.backtrack_limit ছাড়িয়ে যাওয়া এড়াতে - Mpdf তৈরির আগেই বাড়িয়ে দিতে হবে
        ini_set('pcre.backtrack_limit', '10000000');

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

        // Destination::INLINE ব্যবহার করলে mpdf নিজেই সরাসরি header() পাঠিয়ে
        // browser-এ echo করে দেয় - এতে Laravel-এর response() header set করতে
        // গিয়ে "headers already sent" error দেয়। তাই STRING_RETURN দিয়ে raw
        // PDF content আনতে হবে, তারপর নিজেরাই clean response বানাতে হবে।
        $pdfContent = $mpdf->Output('', Destination::STRING_RETURN);

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$quotation->quotation_number.'.pdf"',
        ]);
    }

    private function toBase64(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $imageData = Storage::disk('public')->get($path);
        $mime = Storage::disk('public')->mimeType($path);

        return 'data:'.$mime.';base64,'.base64_encode($imageData);
    }
}