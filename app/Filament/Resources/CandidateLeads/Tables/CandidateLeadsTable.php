<?php

namespace App\Filament\Resources\CandidateLeads\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CandidateLeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('destinationCountry.name')
                    ->label('Destination')
                    ->badge()
                    ->color('info')
                    ->searchable(),

                TextColumn::make('jobCategory.name_en')
                    ->label('Job Category')
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'New' => 'gray',
                        'Contacted' => 'info',
                        'Interested' => 'warning',
                        'Documents Collecting' => 'primary',
                        'Not Interested' => 'danger',
                        'Converted to Worker' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('next_follow_up_date')
                    ->label('Next Follow-up')
                    ->date('d M, Y')
                    ->sortable()
                    ->color(fn ($record) => $record->next_follow_up_date && $record->next_follow_up_date->isPast() ? 'danger' : null),

                TextColumn::make('enteredBy.name')
                    ->label('Entered By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'New' => 'New',
                        'Contacted' => 'Contacted',
                        'Interested' => 'Interested',
                        'Documents Collecting' => 'Documents Collecting',
                        'Not Interested' => 'Not Interested',
                        'Converted to Worker' => 'Converted to Worker',
                    ]),

                SelectFilter::make('destination_country_id')
                    ->label('Destination')
                    ->relationship('destinationCountry', 'name')
                    ->searchable(),

                TernaryFilter::make('due_for_followup')
                    ->label('Due for Follow-up')
                    ->queries(
                        true: fn (Builder $query) => $query->whereDate('next_follow_up_date', '<=', now()),
                        false: fn (Builder $query) => $query,
                    ),
            ])
            ->recordActions([
                Action::make('logFollowUp')
                    ->label('Log Follow-up')
                    ->icon('heroicon-o-phone')
                    ->color('success')
                    ->schema([
                        Textarea::make('note')
                            ->label('কী কথা হয়েছে')
                            ->required()
                            ->rows(3),

                        DatePicker::make('next_follow_up_date')
                            ->label('পরের ফলো-আপ তারিখ'),
                    ])
                    ->action(function (array $data, $record) {
                        $record->followUps()->create([
                            'contacted_by' => auth()->id(),
                            'contacted_at' => now(),
                            'note' => $data['note'],
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? null,
                        ]);

                        $record->update([
                            'status' => $record->status === 'New' ? 'Contacted' : $record->status,
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? $record->next_follow_up_date,
                        ]);

                        Notification::make()
                            ->title('Follow-up logged')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}