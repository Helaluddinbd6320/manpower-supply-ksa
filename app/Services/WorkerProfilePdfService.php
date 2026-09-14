<?php

namespace App\Services;

use App\Enums\IqamaStatus;
use App\Enums\LocationType;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

class WorkerProfilePdfService
{
    /**
     * Target pixel dimensions each image is cropped + resized to before
     * embedding, computed at 200dpi so the print size on the page is
     * *guaranteed* — mPDF's handling of "mm"/object-fit sizing on <img>
     * tags is unreliable and was letting large source photos (e.g. a
     * high-res 3000x4000 phone photo) render at their intrinsic size
     * instead of the frame size. Pre-cropping in PHP removes that
     * dependency entirely: every embedded image already has exactly the
     * right pixel dimensions and aspect ratio for its frame.
     *
     * Standard passport photo size: 40mm x 50mm @ 200dpi.
     */
    protected const PHOTO_WIDTH_PX = 315;

    protected const PHOTO_HEIGHT_PX = 394;

    /**
     * Standard ID card (CR80) size: 85.6mm x 53.98mm @ 200dpi.
     */
    protected const IQAMA_WIDTH_PX = 674;

    protected const IQAMA_HEIGHT_PX = 425;

    protected const JPEG_QUALITY = 78;

    /**
     * Generate a multi-page PDF profile sheet (one page per worker).
     *
     * @param  Collection<int, Worker>  $workers
     */
    public function generate(Collection $workers, bool $includeMobile = true): string
    {
        // Fetching photos/iqamas for many workers over R2 can take a while,
        // especially on a slow connection. Give this specific operation
        // more room than the default 30s so it doesn't fatal-error out.
        set_time_limit(180);

        // Safety net: even after chunking WriteHTML() per worker below,
        // raise the backtrack limit in case a single worker's HTML is
        // still unexpectedly huge.
        ini_set('pcre.backtrack_limit', '10000000');

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 22,
            'margin_footer' => 8,
        ]);

        $mpdf->SetHTMLFooter('
            <table width="100%" style="font-size: 9px; color: #6b7280; border-top: 1px solid #d1d5db; padding-top: 4px;">
                <tr>
                    <td width="70%" style="text-align: left;">
                        <strong style="color: #0B4F3F;">Manpower Supply KSA</strong> — Reliable manpower supply agency, sourcing workers from multiple countries for Saudi Arabia.
                        We have thousands of ready CVs across all categories: Cleaners, Drivers, Technicians, Housemaids, Construction Workers & more.
                        Call / WhatsApp: <strong>+966 54 308 8658</strong>
                    </td>
                    <td width="30%" style="text-align: right;">
                        Page {PAGENO} of {nbpg}
                    </td>
                </tr>
            </table>
        ');

        // Load the stylesheet exactly once, using mPDF's dedicated
        // "header CSS" parse mode. This keeps every subsequent
        // WriteHTML() call to just body markup, so we're not repeating
        // (and re-parsing) the same <style> block for every worker.
        $css = view('pdf.worker-profile-style')->render();
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

        // Write one worker at a time. This is the fix mPDF itself asks
        // for in the "pcre.backtrack_limit" error: keep each WriteHTML()
        // call small instead of concatenating every worker (with their
        // base64 images) into one giant HTML string.
        foreach ($workers as $index => $worker) {
            $isInSaudi = $worker->location_type === LocationType::InSaudiArabia
                || $worker->location_type === LocationType::InSaudiArabia->value;

            $employmentStatusLabel = $worker->employment_status === 'free_available'
                ? 'Free / Available'
                : 'Currently Working';

            $hasNoIqamaOnlyBorderNumber = $worker->iqama_status === IqamaStatus::NoIqamaBorderNumber
                || $worker->iqama_status === IqamaStatus::NoIqamaBorderNumber->value;

            // Built as an ordered list of label/value pairs so the Blade
            // view can lay them out two-per-row (compact 2-column grid)
            // without needing to know which fields exist — this keeps
            // everything fitting on a single page even as fields are added.
            $rows = [
                ['label' => 'Name', 'value' => $worker->name ?: '-'],
            ];

            if ($includeMobile) {
                $rows[] = ['label' => 'Mobile', 'value' => $worker->mobile_number ?: '-'];
            }

            $rows[] = ['label' => 'Job Categories', 'value' => $worker->jobCategories->pluck('name')->implode(', ') ?: '-'];

            // Mirrors the "Direct" placeholder used in WorkersTable for
            // workers with no sourcing_agent_id set.
            $rows[] = ['label' => 'Sourced via Agent', 'value' => $worker->sourcingAgent?->name ?: 'Direct'];

            $rows[] = ['label' => 'Gender', 'value' => $worker->gender ? ucfirst($worker->gender) : '-'];
            $rows[] = ['label' => 'Nationality', 'value' => $worker->nationality?->getLabel() ?? '-'];
            $rows[] = ['label' => 'Employment Status', 'value' => $employmentStatusLabel];
            $rows[] = ['label' => 'Passport Number', 'value' => $worker->passport_number ?: '-'];
            $rows[] = ['label' => 'Passport Expiry', 'value' => $worker->passport_expiry_date?->format('d M Y') ?? '-'];
            $rows[] = ['label' => 'Date of Birth', 'value' => $worker->date_of_birth?->format('d M Y') ?? '-'];
            $rows[] = ['label' => 'Religion', 'value' => $worker->religion ?: '-'];
            $rows[] = ['label' => 'Arabic Proficiency', 'value' => $worker->arabic_proficiency?->getLabel() ?? '-'];
            $rows[] = ['label' => 'English Proficiency', 'value' => $worker->english_proficiency?->getLabel() ?? '-'];
            $rows[] = ['label' => 'Education', 'value' => $worker->education_qualification ?: '-'];

            if ($isInSaudi) {
                // Iqama Status is shown for every in-Saudi worker regardless
                // of location sub-details, since it's the field companies
                // filter/screen candidates on first (valid vs huroob etc.).
                $rows[] = ['label' => 'Iqama Status', 'value' => $worker->iqama_status?->getLabel() ?? '-'];

                if ($hasNoIqamaOnlyBorderNumber) {
                    $rows[] = ['label' => 'Border Number', 'value' => $worker->border_number ?: '-'];
                } else {
                    $rows[] = ['label' => 'Iqama Number', 'value' => $worker->iqama_number ?: '-'];
                    $rows[] = ['label' => 'Iqama Expiry', 'value' => $worker->iqama_expiry_date?->format('d M Y') ?? '-'];
                }

                $rows[] = ['label' => 'Profession (Iqama)', 'value' => $worker->iqama_occupation ?: '-'];
                $rows[] = ['label' => 'Current City', 'value' => $worker->current_city ?: '-'];
            }

            $data = [
                'worker_id' => $worker->worker_id,
                'employment_status' => $employmentStatusLabel,
                'employment_status_class' => $worker->employment_status === 'free_available'
                    ? 'badge-available'
                    : 'badge-working',
                'rows' => $rows,
                'photo_base64' => $this->imageToBase64(
                    $worker->photo?->file_path,
                    self::PHOTO_WIDTH_PX,
                    self::PHOTO_HEIGHT_PX
                ),
                'iqama_base64' => $this->imageToBase64(
                    $worker->iqama?->file_path,
                    self::IQAMA_WIDTH_PX,
                    self::IQAMA_HEIGHT_PX
                ),
            ];

            $html = view('pdf.worker-profile-sheet', [
                'worker' => $data,
                'includeMobile' => $includeMobile,
            ])->render();

            // Start every worker after the first on a fresh page.
            if ($index > 0) {
                $mpdf->AddPage();
            }

            $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'chapaihr_profiles_') . '.pdf';
        $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);

        return $outputPath;
    }

    protected function imageToBase64(?string $filePath, int $targetWidth, int $targetHeight): ?string
    {
        if (! $filePath || ! Storage::disk('r2')->exists($filePath)) {
            return null;
        }

        try {
            $contents = Storage::disk('r2')->get($filePath);
        } catch (\Throwable $e) {
            // R2 fetch failed (network issue, timeout, etc.) — skip this
            // image rather than failing the whole PDF export.
            return null;
        }

        $cropped = $this->cropToCover($contents, $targetWidth, $targetHeight);

        if ($cropped === null) {
            // Fall back to original bytes if GD couldn't process it
            // (e.g. unsupported format) rather than dropping the image.
            // Note: without cropping, this image's on-page size may not
            // exactly match its frame — this path should be rare.
            $mime = $this->guessMimeFromExtension($filePath);

            return 'data:' . $mime . ';base64,' . base64_encode($contents);
        }

        return 'data:image/jpeg;base64,' . base64_encode($cropped);
    }

    /**
     * Center-crop the image to exactly match the target aspect ratio,
     * then resize to the exact target pixel dimensions ("cover" style —
     * same idea as CSS object-fit: cover, but done in PHP so the
     * resulting image is guaranteed to render at a fixed, uniform size
     * regardless of mPDF's image-sizing quirks).
     *
     * Returns null if GD can't read the image.
     */
    protected function cropToCover(string $contents, int $targetWidth, int $targetHeight): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $source = @imagecreatefromstring($contents);

        if ($source === false) {
            return null;
        }

        $srcWidth = imagesx($source);
        $srcHeight = imagesy($source);
        $srcRatio = $srcWidth / $srcHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($srcRatio > $targetRatio) {
            // Source is relatively wider than the target box — crop the
            // left/right edges, keep full height.
            $cropHeight = $srcHeight;
            $cropWidth = (int) round($srcHeight * $targetRatio);
            $srcX = (int) round(($srcWidth - $cropWidth) / 2);
            $srcY = 0;
        } else {
            // Source is relatively taller than the target box — crop
            // top/bottom, keep full width.
            $cropWidth = $srcWidth;
            $cropHeight = (int) round($srcWidth / $targetRatio);
            $srcX = 0;
            $srcY = (int) round(($srcHeight - $cropHeight) / 2);
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        // Flatten transparency onto white (JPEG has no alpha channel).
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            $srcX,
            $srcY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight
        );
        imagedestroy($source);

        ob_start();
        imagejpeg($canvas, null, self::JPEG_QUALITY);
        $output = ob_get_clean();
        imagedestroy($canvas);

        return $output ?: null;
    }

    /**
     * Guess the MIME type from the file extension instead of making a
     * separate R2 metadata request (mimeType()) — halves the number of
     * network round trips needed per image.
     */
    protected function guessMimeFromExtension(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };
    }
}
