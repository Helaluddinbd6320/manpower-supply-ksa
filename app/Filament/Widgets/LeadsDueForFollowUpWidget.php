<?php

namespace App\Filament\Widgets;

use App\Enums\LeadStatus;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LeadsDueForFollowUpWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Leads Due for Follow-up';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->whereDate('next_follow_up_date', '<=', now())
                    ->whereNotIn('status', ['Converted to Client', 'Not Interested'])
            )
            ->columns([
                TextColumn::make('company_name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('contact_person')
                    ->placeholder('—'),
                TextColumn::make('phone_number')
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->icon('heroicon-o-clipboard-document'),
                TextColumn::make('city.name')
                    ->label('City')
                    ->badge(),
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
                            'status' => $record->status === LeadStatus::New ? LeadStatus::Contacted : $record->status,
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? $record->next_follow_up_date,
                        ]);
                    })
                    ->successNotificationTitle('Follow-up logged'),
            ])
            ->defaultSort('next_follow_up_date')
            ->recordUrl(null)
            ->recordAction(null)
            ->paginated([5, 10, 25])
            ->emptyStateHeading('No leads due for follow-up')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}