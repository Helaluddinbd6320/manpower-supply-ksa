<?php

namespace App\Filament\Resources\Placements\RelationManagers;

use App\Enums\PaymentStatus;
use App\Filament\Concerns\HasRoleBasedVisibility;
use App\Models\PlacementMonthlyRecord;
use App\Services\SalarySlipPdfService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MonthlyRecordsRelationManager extends RelationManager
{
    use HasRoleBasedVisibility;

    protected static string $relationship = 'monthlyRecords';

    protected static ?string $title = 'Monthly Records';

    public function form(Schema $schema): Schema
    {
        return $schema->components(self::formSchema($this));
    }

    /**
     * Shared between the create/edit modal form and the standalone
     * form() method — keeps duty_days validation + live preview in sync.
     */
    protected static function formSchema(self $livewire): array
    {
        return [
            Select::make('month')
                ->label('Month')
                ->options([
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                ])
                ->default(now()->month)
                ->required()
                ->live()
                ->native(false),

            TextInput::make('year')
                ->label('Year')
                ->numeric()
                ->default(now()->year)
                ->minValue(2020)
                ->maxValue(2100)
                ->required()
                ->live(),

            TextInput::make('duty_days')
                ->label('Duty Days')
                ->numeric()
                ->minValue(0)
                ->required()
                ->live(onBlur: true)
                ->helperText(function (Get $get) {
                    $daysInMonth = self::daysInMonth($get('month'), $get('year'));

                    return $daysInMonth ? "Max {$daysInMonth} days for this month." : null;
                })
                ->rule(function (Get $get) {
                    $daysInMonth = self::daysInMonth($get('month'), $get('year'));

                    return $daysInMonth ? "max:{$daysInMonth}" : null;
                }),

            Placeholder::make('amount_preview')
                ->label('Prorated Amount Preview')
                ->visible(fn () => self::canViewFinancialFields())
                ->content(function (Get $get) use ($livewire) {
                    $placement = $livewire->getOwnerRecord();
                    $dutyDays = (float) ($get('duty_days') ?? 0);
                    $daysInMonth = self::daysInMonth($get('month'), $get('year'));

                    if (! $daysInMonth || $dutyDays <= 0) {
                        return new HtmlString('<span class="text-sm text-gray-400">Enter duty days to see the amount.</span>');
                    }

                    $billingRate = $placement->client_billing_rate;
                    $payoutRate = $placement->worker_payout_rate;

                    if (is_null($billingRate) || is_null($payoutRate)) {
                        return new HtmlString('<span class="text-sm text-gray-400">Placement rates are not set yet.</span>');
                    }

                    $clientAmount = round(($dutyDays / $daysInMonth) * (float) $billingRate, 2);
                    $workerAmount = round(($dutyDays / $daysInMonth) * (float) $payoutRate, 2);
                    $margin = $clientAmount - $workerAmount;

                    return new HtmlString(
                        "<div class='text-sm space-y-1'>
                            <div>Client Amount: <strong>SAR " . number_format($clientAmount, 2) . "</strong></div>
                            <div>Worker Amount: <strong>SAR " . number_format($workerAmount, 2) . "</strong></div>
                            <div>Margin: <strong class='" . ($margin < 0 ? 'text-danger-600' : 'text-success-600') . "'>SAR " . number_format($margin, 2) . "</strong></div>
                        </div>"
                    );
                })
                ->columnSpanFull(),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->columnSpanFull(),
        ];
    }

    protected static function daysInMonth(mixed $month, mixed $year): ?int
    {
        if (blank($month) || blank($year)) {
            return null;
        }

        try {
            return Carbon::createFromDate((int) $year, (int) $month, 1)->daysInMonth;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('month')
            ->columns([
                TextColumn::make('period')
                    ->label('Month')
                    ->state(fn (PlacementMonthlyRecord $record) => Carbon::createFromDate($record->year, $record->month, 1)->format('F Y'))
                    ->sortable(['year', 'month']),

                TextColumn::make('duty_days')
                    ->label('Duty Days'),

                TextColumn::make('client_amount')
                    ->label('Client Amount')
                    ->money('SAR')
                    ->visible(fn () => self::canViewFinancialFields()),

                TextColumn::make('worker_amount')
                    ->label('Worker Amount')
                    ->money('SAR')
                    ->visible(fn () => self::canViewFinancialFields()),

                TextColumn::make('monthly_margin')
                    ->label('Margin')
                    ->money('SAR')
                    ->visible(fn () => self::canViewFinancialFields()),

                TextColumn::make('client_payment_status')
                    ->label('Client Payment')
                    ->badge(),

                TextColumn::make('worker_payment_status')
                    ->label('Worker Payment')
                    ->badge(),
            ])
            ->recordActions([
                Action::make('toggleClientPaid')
                    ->label(fn (PlacementMonthlyRecord $record) => $record->client_payment_status === PaymentStatus::Paid
                        ? 'Mark Client Unpaid'
                        : 'Mark Client Paid')
                    ->icon('heroicon-o-currency-dollar')
                    ->visible(fn () => self::canViewFinancialFields())
                    ->action(function (PlacementMonthlyRecord $record) {
                        $isPaid = $record->client_payment_status === PaymentStatus::Paid;

                        $record->update([
                            'client_payment_status' => $isPaid ? PaymentStatus::Pending : PaymentStatus::Paid,
                            'client_paid_at' => $isPaid ? null : now(),
                        ]);

                        Notification::make()->success()->title('Client payment status updated')->send();
                    }),

                Action::make('toggleWorkerPaid')
                    ->label(fn (PlacementMonthlyRecord $record) => $record->worker_payment_status === PaymentStatus::Paid
                        ? 'Mark Worker Unpaid'
                        : 'Mark Worker Paid')
                    ->icon('heroicon-o-banknotes')
                    ->visible(fn () => self::canViewFinancialFields())
                    ->action(function (PlacementMonthlyRecord $record) {
                        $isPaid = $record->worker_payment_status === PaymentStatus::Paid;

                        $record->update([
                            'worker_payment_status' => $isPaid ? PaymentStatus::Pending : PaymentStatus::Paid,
                            'worker_paid_at' => $isPaid ? null : now(),
                        ]);

                        Notification::make()->success()->title('Worker payment status updated')->send();
                    }),

                Action::make('downloadSalarySlip')
                    ->label('Salary Slip')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function (PlacementMonthlyRecord $record) {
                        $service = app(SalarySlipPdfService::class);
                        $path = $service->generate($record);

                        if (! $path) {
                            Notification::make()
                                ->danger()
                                ->title('Could not generate salary slip')
                                ->send();

                            return;
                        }

                        $worker = $record->placement->worker;
                        $filename = 'salary-slip-' . Str::slug($worker->worker_id) . '-' . $record->year . '-' . $record->month . '.pdf';

                        return response()->download($path, $filename)->deleteFileAfterSend(true);
                    }),

                EditAction::make()
                    ->schema(fn ($livewire) => self::formSchema($livewire)),

                DeleteAction::make(),
            ])
            ->headerActions([
                \Filament\Actions\CreateAction::make()
                    ->schema(fn ($livewire) => self::formSchema($livewire))
                    ->mutateDataUsing(function (array $data) {
                        $data['entered_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->defaultSort('year', 'desc');
    }
}