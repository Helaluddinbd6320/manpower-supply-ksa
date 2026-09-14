<?php

namespace App\Filament\Resources\SourceAgencies\Tables;

use App\Enums\LeadSource;
use App\Enums\SourceAgencyStatus;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SourceAgenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agency_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->badge()
                    ->sortable(),
                TextColumn::make('contact_person')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone_number')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->icon('heroicon-o-clipboard-document'),
                TextColumn::make('jobCategories.name')
                    ->label('Supplies')
                    ->badge()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->toggleable(),
                TextColumn::make('source')
                    ->badge()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('next_follow_up_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->next_follow_up_date && $record->next_follow_up_date->isPast() ? 'danger' : null),
                TextColumn::make('enteredBy.name')
                    ->label('Entered By')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(SourceAgencyStatus::class),
                SelectFilter::make('source')
                    ->options(LeadSource::class),
                SelectFilter::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jobCategories')
                    ->label('Job Category')
                    ->relationship('jobCategories', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('due_for_follow_up')
                    ->label('Due for Follow-up')
                    ->query(fn (Builder $query) => $query
                        ->whereDate('next_follow_up_date', '<=', now())
                        ->whereNotIn('status', ['Active Partner', 'Not Interested']))
                    ->toggle(),
            ])
            ->recordActions([
                Action::make('logFollowUp')
                    ->label('Log Follow-up')
                    ->icon('heroicon-o-phone')
                    ->color('success')
                    ->schema([
                        Textarea::make('note')
                            ->label('What was discussed?')
                            ->required()
                            ->rows(3),
                        DatePicker::make('next_follow_up_date')
                            ->label('Next Follow-up Date')
                            ->native(false),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->followUps()->create([
                            'contacted_by' => auth()->id(),
                            'contacted_at' => now(),
                            'note' => $data['note'],
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? null,
                        ]);

                        $record->update([
                            'status' => $record->status === SourceAgencyStatus::New ? SourceAgencyStatus::Contacted : $record->status,
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? $record->next_follow_up_date,
                        ]);
                    })
                    ->successNotificationTitle('Follow-up logged'),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('next_follow_up_date')
            ->recordUrl(null)
            ->recordAction(null);
    }
}