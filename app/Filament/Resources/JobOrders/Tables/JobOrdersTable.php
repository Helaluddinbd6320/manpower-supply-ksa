<?php

namespace App\Filament\Resources\JobOrders\Tables;

use App\Enums\OrderStatus;
use App\Models\Worker;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobOrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jobCategory.name')
                    ->label('Job Category')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity_needed')
                    ->label('Qty')
                    ->sortable(),

                TextColumn::make('workers_count')
                    ->label('Shortlisted')
                    ->counts('workers')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('order_date')
                    ->label('Order Date')
                    ->date()
                    ->sortable(),

                TextColumn::make('deadline')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(OrderStatus::class),

                SelectFilter::make('job_category_id')
                    ->label('Job Category')
                    ->relationship('jobCategory', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('shortlistWorkers')
                    ->label('Shortlist Workers')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn ($record) => in_array($record->status, [OrderStatus::Open, OrderStatus::InProgress], true))
                    ->modalHeading(fn ($record) => "Shortlist Workers — {$record->company_name}")
                    ->modalDescription(fn ($record) => "Job Category: {$record->jobCategory->name} • Quantity Needed: {$record->quantity_needed}")
                    ->modalSubmitActionLabel('Add to Shortlist')
                    ->schema(function ($record) {
                        $matching = $record->matchingWorkers()->get(['workers.id', 'workers.worker_id', 'workers.name']);

                        return [
                            CheckboxList::make('worker_ids')
                                ->label('Matching Available Workers')
                                ->options($matching->mapWithKeys(fn (Worker $worker) => [
                                    $worker->id => "{$worker->worker_id} — {$worker->name}",
                                ]))
                                ->helperText($matching->isEmpty()
                                    ? 'No matching available workers found for this category right now.'
                                    : null)
                                ->searchable()
                                ->columns(2)
                                ->required(),
                        ];
                    })
                    ->action(function (array $data, $record) {
                        $selectedIds = $data['worker_ids'] ?? [];

                        $attachData = collect($selectedIds)->mapWithKeys(fn ($id) => [
                            $id => ['status' => 'shortlisted'],
                        ])->all();

                        $record->workers()->attach($attachData);

                        Notification::make()
                            ->success()
                            ->title('Workers shortlisted')
                            ->body(count($selectedIds) . ' worker(s) added to the shortlist.')
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}