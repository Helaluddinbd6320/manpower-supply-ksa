<?php

namespace App\Filament\Widgets;

use App\Models\Agent;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AgentsDueForFollowUpWidget extends BaseWidget
{
    protected static ?int $sort = 8;

    protected int | string | array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return 'Agents Due for Follow-up';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Agent::query()
                    ->where('is_active', true)
                    ->whereDate('next_follow_up_date', '<=', now())
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Agent Name')
                    ->weight('bold')
                    ->searchable(),
                TextColumn::make('mobile_number')
                    ->label('Mobile Number')
                    ->copyable()
                    ->copyMessage('Mobile number copied!')
                    ->icon('heroicon-o-clipboard-document'),
                TextColumn::make('workers_count')
                    ->label('Workers Sourced')
                    ->counts('workers'),
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
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? $record->next_follow_up_date,
                        ]);
                    })
                    ->successNotificationTitle('Follow-up logged'),
            ])
            ->defaultSort('next_follow_up_date')
            ->recordUrl(null)
            ->recordAction(null)
            ->paginated([5, 10, 25])
            ->emptyStateHeading('No agents due for follow-up')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}