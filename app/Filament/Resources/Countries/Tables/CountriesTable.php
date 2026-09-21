<?php

namespace App\Filament\Resources\Countries\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('name')
                    ->label('Country')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('source_agencies_count')
                    ->label('Source Agencies')
                    ->counts('sourceAgencies')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}