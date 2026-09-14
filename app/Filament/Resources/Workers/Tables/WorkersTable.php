<?php

namespace App\Filament\Resources\Workers\Tables;

use App\Enums\LocationType;
use App\Enums\Nationality;
use App\Enums\WorkerStatus;
use App\Services\WorkerProfilePdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\EditAction;
use App\Enums\IqamaStatus;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class WorkersTable
{
    /**
     * Get a cached temporary R2 URL for a file path. Caching the URL itself
     * (not just the file) means repeated page loads within the cache window
     * reuse the exact same signed URL, so the browser can serve the image
     * from its own cache instead of re-downloading it from R2 every time.
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
            ->recordUrl(null)
            ->columns([
                TextColumn::make('worker_id')
                    ->label('Worker ID')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->visible(fn () => session('show_worker_id', false)),

                ImageColumn::make('photo.file_path')
                    ->label('Photo')
                    // ->circular()
                    ->size(80)
                    ->defaultImageUrl(asset('images/placeholder-avatar.png'))
                    ->getStateUsing(fn($record) => $record->photo?->file_path
                        ? self::cachedTemporaryUrl($record->photo->file_path)
                        : null),

                ImageColumn::make('iqama.file_path')
                    ->label('Iqama')
                    ->square()
                    ->imageWidth(120)
                    ->imageHeight(80)
                    ->defaultImageUrl(asset('images/placeholder-document.png'))
                    ->getStateUsing(fn($record) => $record->iqama?->file_path
                        ? self::cachedTemporaryUrl($record->iqama->file_path)
                        : null)
                    ->toggleable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mobile_number')
                    ->label('Mobile')
                    ->searchable()
                    ->visible(fn () => session('show_mobile_number', false)),

                ImageColumn::make('sourcingAgent.avatar_path')
                    ->label('Agent')
                    ->circular()
                    ->size(32)
                    ->defaultImageUrl(asset('images/placeholder-avatar.png'))
                    ->getStateUsing(fn($record) => $record->sourcingAgent?->avatar_path
                        ? self::cachedTemporaryUrl($record->sourcingAgent->avatar_path)
                        : null)
                    ->visible(fn () => session('show_agent', false)),

                TextColumn::make('sourcingAgent.name')
                    ->label('Agent Name')
                    ->placeholder('Direct')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->visible(fn () => session('show_agent', false)),

                TextColumn::make('iqama_status')
                    ->label('Iqama Status')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('iqama_number')
                    ->label('Iqama Number')
                    ->searchable()
                    ->visible(fn () => session('show_iqama_number', false)),

                TextColumn::make('border_number')
                    ->label('Border Number')
                    ->searchable()
                    ->visible(fn () => session('show_iqama_number', false)),

                TextColumn::make('jobCategories.name')
                    ->label('Job Categories')
                    ->badge()
                    ->separator(',')
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->toggleable(),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->colors([
                        'info' => 'male',
                        'warning' => 'female',
                    ])
                    ->toggleable(),

                TextColumn::make('nationality')
                    ->label('Nationality')
                    ->badge()
                    ->sortable()
                    ->toggleable(),

                // NOTE: no ->visible() here. Column-level ->visible() on a
                // Filament table column toggles the WHOLE column for every
                // row at once (it isn't evaluated per-record), so using it
                // with a $record-based condition was hiding the entire
                // Iqama column for all workers instead of just rendering
                // blank for workers without an iqama. The getStateUsing()
                // closure below already returns null when there's no iqama
                // document, and defaultImageUrl() handles that case.


                BadgeColumn::make('employment_status')
                    ->label('Employment')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'free_available' => 'Free / Available',
                        'currently_working' => 'Currently Working',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'free_available',
                        'warning' => 'currently_working',
                    ]),

                BadgeColumn::make('status')
                    ->label('Workflow Status'),

                // kept but hidden by default toggleable columns
                TextColumn::make('passport_number')
                    ->label('Passport No.')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('iqama_expiry_date')
                    ->label('Iqama Expiry')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color(fn($state) => $state && now()->diffInDays($state, false) <= 30 ? 'danger' : null),

                BadgeColumn::make('location_type')
                    ->label('Location')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('enteredBy.name')
                    ->label('Entered By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Entry Date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('jobCategories')
                    ->label('Job Category')
                    ->relationship('jobCategories', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Workflow Status')
                    ->options(WorkerStatus::class),

                SelectFilter::make('location_type')
                    ->label('Location Type')
                    ->options(LocationType::class),
                SelectFilter::make('iqama_status')
                    ->label('Iqama Status')
                    ->options(IqamaStatus::class)
                    ->multiple(),

                SelectFilter::make('employment_status')
                    ->label('Employment Status')
                    ->options([
                        'free_available' => 'Free / Available',
                        'currently_working' => 'Currently Working',
                    ]),

                SelectFilter::make('gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ]),

                SelectFilter::make('nationality')
                    ->label('Nationality')
                    ->options(Nationality::class),

                SelectFilter::make('sourcing_agent_id')
                    ->label('Agent')
                    ->relationship('sourcingAgent', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('experience_years')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('experience_min')
                            ->label('Min Experience (years)')
                            ->numeric(),
                        \Filament\Forms\Components\TextInput::make('experience_max')
                            ->label('Max Experience (years)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['experience_min'],
                                fn(Builder $q, $min) => $q->where('experience_years', '>=', $min),
                            )
                            ->when(
                                $data['experience_max'],
                                fn(Builder $q, $max) => $q->where('experience_years', '<=', $max),
                            );
                    }),

                Filter::make('age_range')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('age_min')
                            ->label('Min Age')
                            ->numeric(),
                        \Filament\Forms\Components\TextInput::make('age_max')
                            ->label('Max Age')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['age_min'],
                                fn(Builder $q, $min) => $q->where('date_of_birth', '<=', now()->subYears($min)),
                            )
                            ->when(
                                $data['age_max'],
                                fn(Builder $q, $max) => $q->where('date_of_birth', '>=', now()->subYears($max)),
                            );
                    }),

                Filter::make('iqama_expiring_soon')
                    ->label('Iqama Expiring in 30 Days')
                    ->query(fn(Builder $query): Builder => $query
                        ->whereNotNull('iqama_expiry_date')
                        ->whereBetween('iqama_expiry_date', [now(), now()->addDays(30)])),
            ])
            ->headerActions([
                Action::make('toggleWorkerId')
                    ->label(fn () => session('show_worker_id', false)
                        ? 'Hide Worker ID'
                        : 'Show Worker ID')
                    ->icon(fn () => session('show_worker_id', false)
                        ? 'heroicon-o-eye-slash'
                        : 'heroicon-o-eye')
                    ->color(fn () => session('show_worker_id', false) ? 'danger' : 'gray')
                    ->action(function () {
                        session(['show_worker_id' => ! session('show_worker_id', false)]);
                    }),

                Action::make('toggleMobileNumber')
                    ->label(fn () => session('show_mobile_number', false)
                        ? 'Hide Mobile Number'
                        : 'Show Mobile Number')
                    ->icon(fn () => session('show_mobile_number', false)
                        ? 'heroicon-o-eye-slash'
                        : 'heroicon-o-eye')
                    ->color(fn () => session('show_mobile_number', false) ? 'danger' : 'gray')
                    ->action(function () {
                        session(['show_mobile_number' => ! session('show_mobile_number', false)]);
                    }),

                Action::make('toggleAgent')
                    ->label(fn () => session('show_agent', false)
                        ? 'Hide Agent'
                        : 'Show Agent')
                    ->icon(fn () => session('show_agent', false)
                        ? 'heroicon-o-eye-slash'
                        : 'heroicon-o-eye')
                    ->color(fn () => session('show_agent', false) ? 'danger' : 'gray')
                    ->action(function () {
                        session(['show_agent' => ! session('show_agent', false)]);
                    }),

                Action::make('toggleIqamaNumber')
                    ->label(fn () => session('show_iqama_number', false)
                        ? 'Hide Iqama/Border No.'
                        : 'Show Iqama/Border No.')
                    ->icon(fn () => session('show_iqama_number', false)
                        ? 'heroicon-o-eye-slash'
                        : 'heroicon-o-eye')
                    ->color(fn () => session('show_iqama_number', false) ? 'danger' : 'gray')
                    ->action(function () {
                        session(['show_iqama_number' => ! session('show_iqama_number', false)]);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ], position: RecordActionsPosition::BeforeColumns)
            ->toolbarActions([
                BulkAction::make('exportProfileSheets')
                    ->label('Export Profile PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->schema([
                        Toggle::make('include_mobile')
                            ->label('Include Mobile Number')
                            ->default(true),
                    ])
                    ->action(function (array $data, $records) {
                        $service = app(WorkerProfilePdfService::class);
                        $path = $service->generate($records, $data['include_mobile']);

                        return response()
                            ->download($path, 'worker-profiles-' . now()->format('Y-m-d-His') . '.pdf')
                            ->deleteFileAfterSend(true);
                    })
                    ->deselectRecordsAfterCompletion(),

                BulkAction::make('exportExcel')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-table-cells')
                    ->schema([
                        Toggle::make('include_mobile')
                            ->label('Include Mobile Number')
                            ->default(true),
                    ])
                    ->action(function (array $data, $records) {
                        return \Maatwebsite\Excel\Facades\Excel::download(
                            new \App\Exports\WorkersExport($records, $data['include_mobile']),
                            'workers-' . now()->format('Y-m-d-His') . '.xlsx'
                        );
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('created_at', 'desc')
            ->searchable();
    }
}