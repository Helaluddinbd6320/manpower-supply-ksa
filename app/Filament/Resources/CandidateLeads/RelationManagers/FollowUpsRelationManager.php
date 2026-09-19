<?php

namespace App\Filament\Resources\CandidateLeads\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FollowUpsRelationManager extends RelationManager
{
    protected static string $relationship = 'followUps';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('note')
                    ->label('কী কথা হয়েছে')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                DatePicker::make('next_follow_up_date')
                    ->label('পরের ফলো-আপ তারিখ'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('note')
            ->columns([
                TextColumn::make('contacted_at')
                    ->label('তারিখ')
                    ->dateTime('d M, Y h:i A')
                    ->sortable(),

                TextColumn::make('note')
                    ->label('নোট')
                    ->wrap()
                    ->limit(100),

                TextColumn::make('contactedBy.name')
                    ->label('যিনি যোগাযোগ করেছেন')
                    ->placeholder('—'),

                TextColumn::make('next_follow_up_date')
                    ->label('পরের ফলো-আপ')
                    ->date('d M, Y')
                    ->placeholder('—'),
            ])
            ->defaultSort('contacted_at', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label('Log Follow-up')
                    ->mutateFormDataUsing(function (array $data) {
                        $data['contacted_by'] = auth()->id();
                        $data['contacted_at'] = now();

                        return $data;
                    }),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}