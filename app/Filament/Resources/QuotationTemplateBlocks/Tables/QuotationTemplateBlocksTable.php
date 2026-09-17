<?php

namespace App\Filament\Resources\QuotationTemplateBlocks\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationTemplateBlocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->badge()
                    ->searchable(),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                ImageColumn::make('image_path')
                    ->label('Preview')
                    ->height(40),

                IconColumn::make('is_default')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'header_image' => 'Header Image',
                        'client_info' => 'Client Info',
                        'terms' => 'Terms & Conditions',
                        'signature' => 'Signature',
                        'footer' => 'Company Footer',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}