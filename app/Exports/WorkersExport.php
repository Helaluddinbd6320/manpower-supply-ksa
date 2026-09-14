<?php

namespace App\Exports;

use App\Enums\IqamaStatus;
use App\Enums\LocationType;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WorkersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents, WithColumnWidths, WithDrawings
{
    /**
     * Photo column is always column C — Worker ID (A) and Name (B) come
     * before it, and both photo columns come before the optional Mobile
     * column, so these letters stay fixed regardless of $includeMobile.
     */
    protected const PHOTO_COLUMN = 'C';

    protected const IQAMA_PHOTO_COLUMN = 'D';

    /**
     * Worker photo — same high-crop-resolution / smaller-display-size
     * approach used for the iqama photo, so facial detail stays sharp
     * even when zoomed in.
     */
    protected const PHOTO_DISPLAY_WIDTH_PX = 90;

    protected const PHOTO_DISPLAY_HEIGHT_PX = 112;

    protected const PHOTO_CROP_WIDTH_PX = 360;

    protected const PHOTO_CROP_HEIGHT_PX = 448;

    /**
     * ID-card (CR80) aspect ratio — 85.6mm x 53.98mm, same proportions
     * used for the iqama frame in the PDF profile sheet.
     *
     * The DISPLAY size is what actually shows up in the cell. The CROP
     * size is deliberately much higher resolution than the display size:
     * PhpSpreadsheet embeds the full-resolution image bytes and only uses
     * width/height as display metadata, so keeping extra pixel detail
     * here means the iqama number stays legible both at normal view and
     * when the user zooms in in Excel — a 160x101 source was simply too
     * low-res for small printed text on the card.
     */
    protected const IQAMA_DISPLAY_WIDTH_PX = 200;

    protected const IQAMA_DISPLAY_HEIGHT_PX = 126;

    protected const IQAMA_CROP_WIDTH_PX = 640;

    protected const IQAMA_CROP_HEIGHT_PX = 404;

    /**
     * Row height in points, sized to comfortably fit the taller of the
     * two images plus a little padding.
     */
    protected const PHOTO_ROW_HEIGHT_PT = 96;

    /**
     * Standard JPEG quality for the worker photo (small, no fine text to
     * preserve). Iqama photos use a higher quality (see drawings()) since
     * compression artifacts make small printed numbers unreadable.
     */
    protected const JPEG_QUALITY = 78;

    protected const PHOTO_JPEG_QUALITY = 88;

    protected const IQAMA_JPEG_QUALITY = 92;

    /**
     * Temp files written for each worker's cropped photos, cleaned up
     * once the full response (including the completed .xlsx write) has
     * been sent — see the constructor.
     *
     * @var array<int, string>
     */
    protected array $tempFiles = [];

    /**
     * @param  Collection<int, Worker>  $workers  Pre-selected records passed
     *                                             in from the table's bulk
     *                                             action — same collection
     *                                             type the PDF service uses.
     */
    public function __construct(
        protected Collection $workers,
        protected bool $includeMobile = true,
    ) {
        // The .xlsx writer reads each drawing's file from disk during the
        // final save step, which happens AFTER the AfterSheet event fires.
        // Cleaning up in app()->terminating() guarantees the full response
        // (including the completed .xlsx write) has already been sent
        // before we touch these files.
        app()->terminating(function () {
            foreach ($this->tempFiles as $tempFile) {
                if (file_exists($tempFile)) {
                    @unlink($tempFile);
                }
            }
        });
    }

    public function collection(): Collection
    {
        return $this->workers;
    }

    public function headings(): array
    {
        $headings = ['Worker ID', 'Name', 'Photo', 'Iqama Photo'];

        if ($this->includeMobile) {
            $headings[] = 'Mobile';
        }

        return [
            ...$headings,
            'Iqama / Border Number',
            'Iqama Status',
            'Iqama Expiry',
            'Current City',
            'Nationality',
            'Job Categories',
            'Agent',
            'Gender',
            'Employment Status',
            'Passport Number',
            'Passport Expiry',
            'Date of Birth',
            'Religion',
            'Arabic Proficiency',
            'English Proficiency',
            'Education',
            'Location',
            'Profession (Iqama)',
        ];
    }

    /**
     * @param  Worker  $worker
     */
    public function map($worker): array
    {
        $isInSaudi = $worker->location_type === LocationType::InSaudiArabia
            || $worker->location_type === LocationType::InSaudiArabia->value;

        $hasNoIqamaOnlyBorderNumber = $worker->iqama_status === IqamaStatus::NoIqamaBorderNumber
            || $worker->iqama_status === IqamaStatus::NoIqamaBorderNumber->value;

        $employmentStatusLabel = $worker->employment_status === 'free_available'
            ? 'Free / Available'
            : 'Currently Working';

        // Photo / Iqama Photo column values stay blank — the actual
        // images are placed on top of these cells separately via
        // drawings(). Row height is set in registerEvents() so both
        // images have room to render.
        $row = [
            $worker->worker_id,
            $worker->name ?: '-',
            '',
            '',
        ];

        if ($this->includeMobile) {
            $row[] = $worker->mobile_number ?: '-';
        }

        return [
            ...$row,
            $isInSaudi
                ? ($hasNoIqamaOnlyBorderNumber
                    ? ($worker->border_number ?: '-')
                    : ($worker->iqama_number ?: '-'))
                : '-',
            $isInSaudi ? ($worker->iqama_status?->getLabel() ?? '-') : '-',
            $isInSaudi ? ($worker->iqama_expiry_date?->format('d M Y') ?? '-') : '-',
            $isInSaudi ? ($worker->current_city ?: '-') : '-',
            $worker->nationality?->getLabel() ?? '-',
            $worker->jobCategories->pluck('name')->implode(', ') ?: '-',
            // Same "Direct" fallback used in WorkersTable and the PDF export.
            $worker->sourcingAgent?->name ?: 'Direct',
            $worker->gender ? ucfirst($worker->gender) : '-',
            $employmentStatusLabel,
            $worker->passport_number ?: '-',
            $worker->passport_expiry_date?->format('d M Y') ?? '-',
            $worker->date_of_birth?->format('d M Y') ?? '-',
            $worker->religion ?: '-',
            $worker->arabic_proficiency?->getLabel() ?? '-',
            $worker->english_proficiency?->getLabel() ?? '-',
            $worker->education_qualification ?: '-',
            $isInSaudi ? 'In Saudi Arabia' : 'Pre-departure',
            $isInSaudi ? ($worker->iqama_occupation ?: '-') : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            // Fixed widths for the two photo columns — ShouldAutoSize
            // would otherwise shrink them back down since the cell text
            // is blank.
            self::PHOTO_COLUMN => 14,
            self::IQAMA_PHOTO_COLUMN => 29,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Two Drawings per worker (Photo + Iqama Photo), anchored to that
     * worker's row. A worker missing either image simply gets no drawing
     * for that column — no error, just a blank cell.
     */
    public function drawings(): array
    {
        $drawings = [];

        foreach ($this->workers->values() as $index => $worker) {
            $rowNumber = $index + 2; // Row 1 is the heading row.

            $photoPath = $this->croppedImageTempFile(
                $worker->photo?->file_path,
                self::PHOTO_CROP_WIDTH_PX,
                self::PHOTO_CROP_HEIGHT_PX,
                self::PHOTO_JPEG_QUALITY
            );

            if ($photoPath) {
                $drawing = new Drawing();
                $drawing->setName($worker->worker_id ?? 'Photo');
                $drawing->setDescription('Worker photo');
                $drawing->setPath($photoPath);
                $drawing->setHeight(self::PHOTO_DISPLAY_HEIGHT_PX);
                $drawing->setWidth(self::PHOTO_DISPLAY_WIDTH_PX);
                $drawing->setCoordinates(self::PHOTO_COLUMN . $rowNumber);
                $drawing->setOffsetX(4);
                $drawing->setOffsetY(4);
                $drawings[] = $drawing;
            }

            $iqamaPath = $this->croppedImageTempFile(
                $worker->iqama?->file_path,
                self::IQAMA_CROP_WIDTH_PX,
                self::IQAMA_CROP_HEIGHT_PX,
                self::IQAMA_JPEG_QUALITY
            );

            if ($iqamaPath) {
                $iqamaDrawing = new Drawing();
                $iqamaDrawing->setName(($worker->worker_id ?? 'Worker') . ' Iqama');
                $iqamaDrawing->setDescription('Iqama / ID card photo');
                $iqamaDrawing->setPath($iqamaPath);
                $iqamaDrawing->setHeight(self::IQAMA_DISPLAY_HEIGHT_PX);
                $iqamaDrawing->setWidth(self::IQAMA_DISPLAY_WIDTH_PX);
                $iqamaDrawing->setCoordinates(self::IQAMA_PHOTO_COLUMN . $rowNumber);
                $iqamaDrawing->setOffsetX(4);
                $iqamaDrawing->setOffsetY(4);
                $drawings[] = $iqamaDrawing;
            }
        }

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $headings = $this->headings();
                $lastColumnLetter = Coordinate::stringFromColumnIndex(count($headings));
                $lastRow = $this->workers->count() + 1;

                // Center every cell (horizontal + vertical) across the
                // whole used range, header row included.
                $sheet->getStyle('A1:' . $lastColumnLetter . $lastRow)
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER);

                // Bold the columns worth scanning/identifying at a glance
                // — mirrors how Worker ID and the status badge are made
                // prominent in the PDF profile sheet. Looked up by heading
                // label (not a hardcoded letter) so this stays correct
                // whether or not the Mobile column is included.
                $boldHeadings = ['Worker ID', 'Name', 'Employment Status', 'Iqama Status'];

                foreach ($boldHeadings as $headingLabel) {
                    $columnIndex = array_search($headingLabel, $headings, true);

                    if ($columnIndex === false) {
                        continue;
                    }

                    $columnLetter = Coordinate::stringFromColumnIndex($columnIndex + 1);

                    $sheet->getStyle($columnLetter . '2:' . $columnLetter . $lastRow)
                        ->getFont()
                        ->setBold(true);
                }

                // Freeze the header row so it stays visible while scrolling
                // through a large worker list.
                $event->sheet->freezePane('E2');

                // Give every data row enough height to actually show both
                // images instead of clipping them.
                $rowCount = $this->workers->count();

                for ($row = 2; $row <= $rowCount + 1; $row++) {
                    $sheet->getRowDimension($row)
                        ->setRowHeight(self::PHOTO_ROW_HEIGHT_PT);
                }

                // NOTE: temp image files are intentionally NOT deleted here.
                // The .xlsx writer still needs to read them from disk after
                // this event fires (see app()->terminating() in the
                // constructor for the actual cleanup).
            },
        ];
    }

    /**
     * Download an image from R2, center-crop + resize it to the given
     * target pixel dimensions, and write it to a temp file (Drawing::
     * setPath() needs a real file path, not raw bytes). Returns null if
     * there's no file or it can't be read.
     */
    protected function croppedImageTempFile(?string $filePath, int $targetWidth, int $targetHeight, int $quality = self::JPEG_QUALITY): ?string
    {
        if (! $filePath || ! Storage::disk('r2')->exists($filePath)) {
            return null;
        }

        try {
            $contents = Storage::disk('r2')->get($filePath);
        } catch (\Throwable $e) {
            return null;
        }

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
            $cropHeight = $srcHeight;
            $cropWidth = (int) round($srcHeight * $targetRatio);
            $srcX = (int) round(($srcWidth - $cropWidth) / 2);
            $srcY = 0;
        } else {
            $cropWidth = $srcWidth;
            $cropHeight = (int) round($srcWidth / $targetRatio);
            $srcX = 0;
            $srcY = (int) round(($srcHeight - $cropHeight) / 2);
        }

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
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

        $tempPath = tempnam(sys_get_temp_dir(), 'chapaihr_xlsx_img_') . '.jpg';
        imagejpeg($canvas, $tempPath, $quality);
        imagedestroy($canvas);

        $this->tempFiles[] = $tempPath;

        return $tempPath;
    }
}