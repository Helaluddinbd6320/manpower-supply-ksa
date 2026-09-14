<?php

namespace App\Filament\Resources\SourceAgencies\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FollowUpsRelationManager extends RelationManager
{
    protected static string $relationship = 'followUps';

    protected static ?string $title = 'Follow-up History';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            DateTimePicker::make('contacted_at')
                ->required()
                ->native(false)
                ->default(now()),
            Textarea::make('note')
                ->rows(3)
                ->columnSpanFull(),
            DatePicker::make('next_follow_up_date')
                ->native(false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->columns([
                TextColumn::make('contacted_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('contactedBy.name')
                    ->label('Staff')
                    ->placeholder('—'),
                TextColumn::make('note')
                    ->wrap()
                    ->limit(80),
                TextColumn::make('next_follow_up_date')
                    ->date()
                    ->placeholder('—'),
            ])
            ->defaultSort('contacted_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['contacted_by'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}