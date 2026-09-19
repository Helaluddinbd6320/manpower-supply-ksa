<?php

namespace App\Filament\Resources\CandidateLeads\Tables;

use App\Services\CandidateLeadProfilePdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CandidateLeadsTable
{
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
            ->recordUrl(null)
            ->columns([
                ImageColumn::make('photo_path')
                    ->label('Photo')
                    ->size(60)
                    ->defaultImageUrl(asset('images/placeholder-avatar.png'))
                    ->getStateUsing(fn ($record) => $record->photo_path
                        ? self::cachedTemporaryUrl($record->photo_path)
                        : null),

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

                Action::make('downloadPdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(fn ($record) => route('candidate-leads.pdf', $record))
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('exportProfileSheets')
                    ->label('Export Profile PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(function ($records) {
                        $service = app(CandidateLeadProfilePdfService::class);
                        $path = $service->generate($records);

                        return response()
                            ->download($path, 'candidate-profiles-' . now()->format('Y-m-d-His') . '.pdf')
                            ->deleteFileAfterSend(true);
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }
}