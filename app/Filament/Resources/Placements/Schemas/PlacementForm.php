<?php

namespace App\Filament\Resources\Placements\Schemas;

use App\Enums\PlacementStatus;
use App\Filament\Concerns\HasRoleBasedVisibility;
use App\Models\JobOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PlacementForm
{
    use HasRoleBasedVisibility;

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Placement Details')
                    ->columns(2)
                    ->schema([
                        Select::make('worker_id')
                            ->label('Worker')
                            ->relationship('worker', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->worker_id} — {$record->name}")
                            ->searchable(['name', 'worker_id'])
                            ->preload()
                            ->required(),

                        Select::make('job_order_id')
                            ->label('Job Order')
                            ->relationship('jobOrder', 'company_name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                if (! $state) {
                                    return;
                                }

                                $order = JobOrder::find($state);

                                if ($order && blank($get('client_company_name'))) {
                                    $set('client_company_name', $order->company_name);
                                }
                            }),

                        TextInput::make('client_company_name')
                            ->label('Client Company Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->default(now())
                            ->required(),

                        DatePicker::make('contract_end_date')
                            ->label('Contract End Date'),

                        Select::make('status')
                            ->label('Status')
                            ->options(PlacementStatus::class)
                            ->default(PlacementStatus::Active)
                            ->required()
                            ->native(false),
                    ]),

                Section::make('Billing & Payout')
                    ->columns(2)
                    ->visible(fn () => self::canViewFinancialFields())
                    ->schema([
                        TextInput::make('client_billing_rate')
                            ->label('Client Billing Rate (SAR / month)')
                            ->numeric()
                            ->prefix('SAR')
                            ->required(fn () => self::canEditPlacementFinancials())
                            ->live(onBlur: true)
                            ->disabled(fn () => ! self::canEditPlacementFinancials()),

                        TextInput::make('worker_payout_rate')
                            ->label('Worker Payout Rate (SAR / month)')
                            ->numeric()
                            ->prefix('SAR')
                            ->required(fn () => self::canEditPlacementFinancials())
                            ->live(onBlur: true)
                            ->disabled(fn () => ! self::canEditPlacementFinancials()),

                        Placeholder::make('margin_preview')
                            ->label('Monthly Margin (Profit)')
                            ->content(function (Get $get) {
                                $billingRaw = $get('client_billing_rate');
                                $payoutRaw = $get('worker_payout_rate');

                                if (blank($billingRaw) && blank($payoutRaw)) {
                                    return new HtmlString(
                                        '<span class="text-sm text-gray-400">Enter both rates to see the margin.</span>'
                                    );
                                }

                                $billing = (float) ($billingRaw ?? 0);
                                $payout = (float) ($payoutRaw ?? 0);
                                $margin = $billing - $payout;

                                $color = $margin < 0 ? 'text-danger-600' : 'text-success-600';
                                $formatted = number_format($margin, 2);

                                return new HtmlString(
                                    "<span class=\"text-lg font-semibold {$color}\">SAR {$formatted}</span>"
                                );
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}