<?php

namespace App\Filament\Resources\Placements\Tables;

use App\Enums\PlacementStatus;
use App\Filament\Concerns\HasRoleBasedVisibility;
use App\Models\Placement;
use App\Services\InvoicePdfService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PlacementsTable
{
    use HasRoleBasedVisibility;

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('worker.worker_id')
                    ->label('Worker ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('worker.name')
                    ->label('Worker Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('client_company_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('contract_end_date')
                    ->label('Contract End')
                    ->date()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('client_billing_rate')
                    ->label('Billing Rate')
                    ->money('SAR')
                    ->sortable()
                    ->visible(fn () => self::canViewFinancialFields()),

                TextColumn::make('worker_payout_rate')
                    ->label('Payout Rate')
                    ->money('SAR')
                    ->sortable()
                    ->visible(fn () => self::canViewFinancialFields()),

                TextColumn::make('monthly_margin')
                    ->label('Margin')
                    ->money('SAR')
                    ->visible(fn () => self::canViewFinancialFields()),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(PlacementStatus::class),
            ])
            ->headerActions([
                Action::make('generateInvoice')
                    ->label('Generate Invoice')
                    ->icon('heroicon-o-document-text')
                    ->visible(fn () => self::canViewFinancialFields())
                    ->schema([
                        Select::make('client_company_name')
                            ->label('Client Company')
                            ->options(fn () => Placement::query()
                                ->distinct()
                                ->pluck('client_company_name', 'client_company_name'))
                            ->searchable()
                            ->required(),

                        Select::make('month')
                            ->label('Month')
                            ->options([
                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                            ])
                            ->default(now()->month)
                            ->required()
                            ->native(false),

                        TextInput::make('year')
                            ->label('Year')
                            ->numeric()
                            ->default(now()->year)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $service = app(InvoicePdfService::class);
                        $result = $service->generate($data['client_company_name'], (int) $data['month'], (int) $data['year']);

                        if (! $result['path']) {
                            Notification::make()
                                ->danger()
                                ->title('No records found')
                                ->body('No monthly records found for this client and period.')
                                ->send();

                            return;
                        }

                        return response()
                            ->download(
                                $result['path'],
                                'invoice-' . Str::slug($data['client_company_name']) . '-' . $data['year'] . '-' . $data['month'] . '.pdf'
                            )
                            ->deleteFileAfterSend(true);
                    }),
            ])
            ->defaultSort('start_date', 'desc');
    }
}