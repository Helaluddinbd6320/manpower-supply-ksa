<?php

namespace App\Filament\Resources\LandingPages\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class LandingPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('h1_heading')
                    ->label('Page')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('URL')
                    ->prefix('/')
                    ->copyable()
                    ->color('gray'),

                TextColumn::make('page_type')
                    ->label('Type')
                    ->badge(),

                IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('page_type')
                    ->options([
                        'city' => 'City',
                        'category' => 'Category',
                        'city_category' => 'City + Category',
                        'custom' => 'Custom',
                    ]),

                TernaryFilter::make('is_published')
                    ->label('Published Status'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}