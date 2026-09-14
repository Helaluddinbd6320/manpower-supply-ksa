<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Filament\Concerns\HasRoleBasedVisibility;
use App\Models\PlacementMonthlyRecord;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingPaymentsWidget extends BaseWidget
{
    use HasRoleBasedVisibility;

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return self::canViewFinancialFields();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Pending Payments')
            ->query(
                PlacementMonthlyRecord::query()
                    ->where(function ($query) {
                        $query->where('client_payment_status', PaymentStatus::Pending)
                            ->orWhere('worker_payment_status', PaymentStatus::Pending);
                    })
                    ->with(['placement.worker'])
            )
            ->columns([
                TextColumn::make('placement.worker.worker_id')
                    ->label('Worker ID'),

                TextColumn::make('placement.worker.name')
                    ->label('Worker Name'),

                TextColumn::make('placement.client_company_name')
                    ->label('Client'),

                TextColumn::make('period')
                    ->label('Month')
                    ->state(fn (PlacementMonthlyRecord $record) => Carbon::createFromDate($record->year, $record->month, 1)->format('F Y')),

                TextColumn::make('client_amount')
                    ->label('Client Amount')
                    ->money('SAR'),

                TextColumn::make('client_payment_status')
                    ->label('Client Payment')
                    ->badge(),

                TextColumn::make('worker_amount')
                    ->label('Worker Amount')
                    ->money('SAR'),

                TextColumn::make('worker_payment_status')
                    ->label('Worker Payment')
                    ->badge(),
            ])
            ->recordActions([
                Action::make('markClientPaid')
                    ->label('Mark Client Paid')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('success')
                    ->visible(fn (PlacementMonthlyRecord $record) => $record->client_payment_status === PaymentStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (PlacementMonthlyRecord $record) {
                        $record->update([
                            'client_payment_status' => PaymentStatus::Paid,
                            'client_paid_at' => now(),
                        ]);

                        Notification::make()->success()->title('Client payment marked as paid')->send();
                    }),

                Action::make('markWorkerPaid')
                    ->label('Mark Worker Paid')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (PlacementMonthlyRecord $record) => $record->worker_payment_status === PaymentStatus::Pending)
                    ->requiresConfirmation()
                    ->action(function (PlacementMonthlyRecord $record) {
                        $record->update([
                            'worker_payment_status' => PaymentStatus::Paid,
                            'worker_paid_at' => now(),
                        ]);

                        Notification::make()->success()->title('Worker payment marked as paid')->send();
                    }),
            ])
            ->defaultSort('year', 'desc')
            ->paginated([5, 10, 25]);
    }
}