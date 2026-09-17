<?php

namespace App\Filament\Resources\Quotations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation_number')
                    ->label('Quotation #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                TextColumn::make('client_company_name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lead.company_name')
                    ->label('Lead')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('quotation_date')
                    ->label('Date')
                    ->date('d M, Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'Draft' => 'gray',
                        'Sent' => 'info',
                        'Accepted' => 'success',
                        'Rejected' => 'danger',
                        'Expired' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('grand_total')
                    ->label('Grand Total / Month')
                    ->state(fn ($record) => 'SAR ' . number_format($record->grand_total, 2))
                    ->weight('semibold'),

                TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M, Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Sent' => 'Sent',
                        'Accepted' => 'Accepted',
                        'Rejected' => 'Rejected',
                        'Expired' => 'Expired',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}