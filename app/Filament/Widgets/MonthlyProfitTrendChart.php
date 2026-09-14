<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\HasRoleBasedVisibility;
use App\Models\PlacementMonthlyRecord;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class MonthlyProfitTrendChart extends ChartWidget
{
    use HasRoleBasedVisibility;

    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        return 'Monthly Profit Trend (Last 6 Months)';
    }

    public static function canView(): bool
    {
        return self::canViewFinancialFields();
    }

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i));

        $labels = $months->map(fn (Carbon $m) => $m->format('M Y'))->toArray();

        $margins = $months->map(function (Carbon $m) {
            $records = PlacementMonthlyRecord::query()
                ->where('month', $m->month)
                ->where('year', $m->year)
                ->get();

            return (float) ($records->sum('client_amount') - $records->sum('worker_amount'));
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Net Margin (SAR)',
                    'data' => $margins,
                    'borderColor' => '#0f766e',
                    'backgroundColor' => 'rgba(15, 118, 110, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}