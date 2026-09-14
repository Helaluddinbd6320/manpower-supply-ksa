<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Concerns\HasRoleBasedVisibility;
use App\Models\PlacementMonthlyRecord;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialSummaryStats extends BaseWidget
{
    use HasRoleBasedVisibility;

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return self::canViewFinancialFields();
    }

    protected function getStats(): array
    {
        $records = PlacementMonthlyRecord::query()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get();

        $totalBilling = $records->sum('client_amount');
        $totalPayout = $records->sum('worker_amount');
        $netMargin = $totalBilling - $totalPayout;

        $pendingCount = $records->where('client_payment_status', PaymentStatus::Pending)->count()
            + $records->where('worker_payment_status', PaymentStatus::Pending)->count();

        return [
            Stat::make('Placed This Month', $records->count())
                ->icon('heroicon-o-user-group')
                ->color('gray'),

            Stat::make('Total Billing', 'SAR ' . number_format($totalBilling, 2))
                ->icon('heroicon-o-arrow-trending-up')
                ->color('info'),

            Stat::make('Total Payout', 'SAR ' . number_format($totalPayout, 2))
                ->icon('heroicon-o-arrow-trending-down')
                ->color('warning'),

            Stat::make('Net Margin', 'SAR ' . number_format($netMargin, 2))
                ->icon('heroicon-o-banknotes')
                ->color($netMargin < 0 ? 'danger' : 'success'),

            Stat::make('Pending Payments', $pendingCount)
                ->icon('heroicon-o-exclamation-circle')
                ->color($pendingCount > 0 ? 'danger' : 'success'),
        ];
    }
}