<?php

namespace App\Filament\Widgets;

use App\Enums\LocationType;
use App\Models\Worker;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class WorkerOverviewStats extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Workers', Worker::count())
                ->icon('heroicon-o-users')
                ->color('gray'),

            Stat::make('Free / Available', Worker::where('employment_status', 'free_available')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Currently Working (Saudi)', Worker::where('employment_status', 'currently_working')
                ->where('location_type', LocationType::InSaudiArabia->value)
                ->count())
                ->icon('heroicon-o-briefcase')
                ->color('info'),

            Stat::make('Pre-departure', Worker::where(function ($query) {
                $query->where('location_type', LocationType::PreDeparture->value)
                    ->orWhereNull('location_type')
                    ->orWhere('location_type', '');
            })->count())
                ->icon('heroicon-o-paper-airplane')
                ->color('warning'),

            Stat::make('New This Month', Worker::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count())
                ->icon('heroicon-o-user-plus')
                ->color('primary'),
        ];
    }
}
