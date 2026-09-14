<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\PlacementMonthlyRecord;
use Carbon\Carbon;
use Mpdf\Mpdf;

class SalarySlipPdfService
{
    public function generate(PlacementMonthlyRecord $record): ?string
    {
        $placement = $record->placement;
        $worker = $placement?->worker;

        if (! $placement || ! $worker) {
            return null;
        }

        $periodLabel = Carbon::createFromDate($record->year, $record->month, 1)->format('F Y');
        $isPaid = $record->worker_payment_status === PaymentStatus::Paid;

        $html = view('pdf.salary-slip', [
            'workerId' => $worker->worker_id,
            'workerName' => $worker->name,
            'clientCompanyName' => $placement->client_company_name,
            'periodLabel' => $periodLabel,
            'dutyDays' => $record->duty_days,
            'daysInMonth' => $record->days_in_month,
            'payoutRate' => (float) ($record->worker_payout_rate_snapshot ?? 0),
            'amount' => (float) ($record->worker_amount ?? 0),
            'isPaid' => $isPaid,
            'paidAt' => $record->worker_paid_at?->format('d M Y'),
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A5',
            'margin_top' => 15,
            'margin_bottom' => 15,
        ]);
        $mpdf->WriteHTML($html);

        $outputPath = tempnam(sys_get_temp_dir(), 'chapaihr_salary_slip_') . '.pdf';
        $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);

        return $outputPath;
    }
}