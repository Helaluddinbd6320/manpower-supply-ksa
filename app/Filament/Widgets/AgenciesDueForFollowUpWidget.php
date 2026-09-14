<?php

namespace App\Filament\Widgets;

use App\Enums\SourceAgencyStatus;
use App\Models\SourceAgency;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AgenciesDueForFollowUpWidget extends BaseWidget
{
    protected static ?int $sort = 7;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Source Agencies Due for Follow-up';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SourceAgency::query()
                    ->whereDate('next_follow_up_date', '<=', now())
                    ->whereNotIn('status', ['Active Partner', 'Not Interested'])
            )
            ->columns([
                TextColumn::make('agency_name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->badge(),
                TextColumn::make('contact_person')
                    ->placeholder('—'),
                TextColumn::make('phone_number')
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->icon('heroicon-o-clipboard-document'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('next_follow_up_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable()
                    ->color('danger'),
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
            ])
            ->defaultSort('next_follow_up_date')
            ->recordUrl(null)
            ->recordAction(null)
            ->paginated([5, 10, 25])
            ->emptyStateHeading('No agencies due for follow-up')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}