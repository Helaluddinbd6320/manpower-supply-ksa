<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\JobOrder;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JobOrderOverviewStats extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        return [
            Stat::make('Open Job Orders', JobOrder::where('status', OrderStatus::Open->value)->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('info'),

            Stat::make('Fulfilled This Month', JobOrder::where('status', OrderStatus::Fulfilled->value)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count())
                ->icon('heroicon-o-check-badge')
                ->color('success'),
        ];
    }
}