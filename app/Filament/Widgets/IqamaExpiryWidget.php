<?php

namespace App\Filament\Widgets;

use App\Models\Worker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class IqamaExpiryWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Iqama Expiring Soon (within 30 days)')
            ->query(
                Worker::query()
                    ->whereNotNull('iqama_expiry_date')
                    ->where('iqama_expiry_date', '<=', now()->addDays(30))
            )
            ->columns([
                TextColumn::make('worker_id')
                    ->label('Worker ID'),

                TextColumn::make('name')
                    ->label('Name'),

                TextColumn::make('current_city')
                    ->label('City')
                    ->placeholder('-'),

                TextColumn::make('iqama_expiry_date')
                    ->label('Iqama Expiry')
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state && now()->greaterThan($state) ? 'danger' : 'warning')
                    ->weight('bold'),
            ])
            ->defaultSort('iqama_expiry_date', 'asc')
            ->paginated([5, 10, 25]);
    }
}