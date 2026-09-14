<?php

namespace App\Services;

use App\Models\PlacementMonthlyRecord;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class InvoicePdfService
{
    /**
     * Generate a client invoice PDF listing all workers placed with the
     * given client for the given month/year, with a total billing amount.
     *
     * @return array{path: ?string, total: float, count: int}
     */
    public function generate(string $clientCompanyName, int $month, int $year): array
    {
        $records = PlacementMonthlyRecord::query()
            ->whereHas('placement', fn ($q) => $q->where('client_company_name', $clientCompanyName))
            ->where('month', $month)
            ->where('year', $year)
            ->whereNotNull('client_amount')
            ->with(['placement.worker.jobCategories'])
            ->get();

        if ($records->isEmpty()) {
            return ['path' => null, 'total' => 0, 'count' => 0];
        }

        $items = $records->map(function (PlacementMonthlyRecord $record) {
            $worker = $record->placement->worker;

            return [
                'worker_id' => $worker->worker_id,
                'worker_name' => $worker->name,
                'job_categories' => $worker->jobCategories->pluck('name')->implode(', ') ?: '-',
                'duty_days' => $record->duty_days,
                'rate' => (float) $record->client_billing_rate_snapshot,
                'amount' => (float) $record->client_amount,
            ];
        });

        $totalAmount = $items->sum('amount');

        $periodLabel = Carbon::createFromDate($year, $month, 1)->format('F Y');
        $invoiceNumber = 'INV-' . Str::upper(Str::slug($clientCompanyName, '')) . '-' . Carbon::createFromDate($year, $month, 1)->format('Ym') . '-' . now()->format('His');

        $html = view('pdf.monthly-invoice', [
            'invoiceNumber' => $invoiceNumber,
            'clientCompanyName' => $clientCompanyName,
            'periodLabel' => $periodLabel,
            'issueDate' => now()->format('d M Y'),
            'items' => $items,
            'totalAmount' => $totalAmount,
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A4',
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        $mpdf->WriteHTML($html);

        $outputPath = tempnam(sys_get_temp_dir(), 'chapaihr_invoice_') . '.pdf';
        $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);

        return [
            'path' => $outputPath,
            'total' => $totalAmount,
            'count' => $items->count(),
        ];
    }
}