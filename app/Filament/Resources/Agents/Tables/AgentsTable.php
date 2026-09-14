<?php

namespace App\Filament\Resources\Agents\Tables;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AgentsTable
{
    /**
     * Cached temporary R2 URL, same pattern as WorkersTable, so repeated
     * page loads within the cache window reuse the same signed URL.
     */
    protected static function cachedTemporaryUrl(string $filePath): string
    {
        $cacheKey = 'r2-temp-url:' . md5($filePath);

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($filePath) {
            return Storage::disk('r2')->temporaryUrl($filePath, now()->addHours(6)->addMinutes(5));
        });
    }

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_path')
                    ->label('Photo')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(asset('images/placeholder-avatar.png'))
                    ->getStateUsing(fn($record) => $record->avatar_path
                        ? self::cachedTemporaryUrl($record->avatar_path)
                        : null),

                TextColumn::make('name')
                    ->label('Agent Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mobile_number')
                    ->label('Mobile Number')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Mobile number copied!')
                    ->copyMessageDuration(1500)
                    ->icon('heroicon-o-clipboard-document'),

                TextColumn::make('workers_count')
                    ->label('Workers Sourced')
                    ->counts('workers')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('next_follow_up_date')
                    ->label('Next Follow-up')
                    ->date()
                    ->sortable()
                    ->placeholder('—')
                    ->color(fn($record) => $record->next_follow_up_date && $record->next_follow_up_date->isPast() ? 'danger' : null),

                TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Filter::make('due_for_follow_up')
                    ->label('Due for Follow-up')
                    ->query(fn(Builder $query) => $query->whereDate('next_follow_up_date', '<=', now()))
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
                            'next_follow_up_date' => $data['next_follow_up_date'] ?? $record->next_follow_up_date,
                        ]);
                    })
                    ->successNotificationTitle('Follow-up logged'),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name')
            ->recordUrl(null)
            ->recordAction(null);
    }
}
