<?php

namespace App\Filament\Widgets;

use App\Models\CandidateLead;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CandidateLeadsFollowUpWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected static ?string $heading = 'Candidates Due for Follow-up';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CandidateLead::query()
                    ->whereDate('next_follow_up_date', '<=', now())
                    ->whereNotIn('status', ['Converted to Worker', 'Not Interested'])
                    ->orderBy('next_follow_up_date')
            )
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('phone_number')->label('Phone'),
                TextColumn::make('destinationCountry.name')->label('Destination')->badge(),
                TextColumn::make('status')->badge(),
                TextColumn::make('next_follow_up_date')->date('d M, Y')->color('danger'),
            ])
            ->paginated([5, 10]);
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user?->can('view candidate leads') || $user?->can('manage candidate leads');
    }
}