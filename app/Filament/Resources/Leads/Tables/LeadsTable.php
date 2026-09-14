<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Enums\LeadSource;
use App\Enums\LeadStatus;
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

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('contact_person')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone_number')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-clipboard-document'),
                TextColumn::make('city.name')
                    ->label('City')
                    ->badge()
                    ->sortable(),
                TextColumn::make('business_category')
                    ->searchable()
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
                    ->options(LeadStatus::class),
                SelectFilter::make('source')
                    ->options(LeadSource::class),
                SelectFilter::make('saudi_city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('due_for_follow_up')
                    ->label('Due for Follow-up')
                    ->query(fn (Builder $query) => $query
                        ->whereDate('next_follow_up_date', '<=', now())
                        ->whereNotIn('status', ['Converted to Client', 'Not Interested']))
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
                            'status' => $record->status === LeadStatus::New ? LeadStatus::Contacted : $record->status,
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