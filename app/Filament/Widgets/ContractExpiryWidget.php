<?php

namespace App\Filament\Widgets;

use App\Enums\PlacementStatus;
use App\Models\Placement;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ContractExpiryWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Contracts Ending Soon (within 30 days)')
            ->query(
                Placement::query()
                    ->where('status', PlacementStatus::Active->value)
                    ->whereNotNull('contract_end_date')
                    ->where('contract_end_date', '<=', now()->addDays(30))
                    ->with('worker')
            )
            ->columns([
                TextColumn::make('worker.worker_id')
                    ->label('Worker ID'),

                TextColumn::make('worker.name')
                    ->label('Worker Name'),

                TextColumn::make('client_company_name')
                    ->label('Client'),

                TextColumn::make('contract_end_date')
                    ->label('Contract End')
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state && now()->greaterThan($state) ? 'danger' : 'warning')
                    ->weight('bold'),
            ])
            ->defaultSort('contract_end_date', 'asc')
            ->paginated([5, 10, 25]);
    }
}