<?php

namespace App\Filament\Resources\JobOrders\RelationManagers;

use App\Enums\WorkerOrderStatus;
use App\Exports\WorkersExport;
use App\Models\Worker;
use App\Services\WorkerProfilePdfService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class WorkersRelationManager extends RelationManager
{
    protected static string $relationship = 'workers';

    protected static ?string $title = 'Shortlisted Workers';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('worker_id')
                    ->label('Worker ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mobile_number')
                    ->label('Mobile'),

                TextColumn::make('pivot.status')
                    ->label('Status')
                    ->badge(),

                IconColumn::make('confirmed_elsewhere')
                    ->label('')
                    ->tooltip('Already Confirmed on another job order')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->state(fn (?Worker $record) => $record?->isConfirmedOnOtherOrder($this->getOwnerRecord()->id) ?? false)
                    ->visible(fn (?Worker $record) => $record?->isConfirmedOnOtherOrder($this->getOwnerRecord()->id) ?? false)
                    ->extraAttributes(['class' => 'w-6']),

                TextColumn::make('pivot.contacted_at')
                    ->label('Contacted At')
                    ->dateTime()
                    ->placeholder('Not yet'),

                TextColumn::make('pivot.contactedBy.name')
                    ->label('Contacted By')
                    ->placeholder('-'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(WorkerOrderStatus::class)
                    ->query(function ($query, array $data) {
                        if (blank($data['value'] ?? null)) {
                            return $query;
                        }

                        $query->wherePivot('status', $data['value']);
                    }),
            ])
            ->recordActions([
                Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading(fn ($record) => "Update Status — {$record->name}")
                    ->fillForm(fn ($record) => [
                        'status' => $record->pivot->status,
                        'contacted_at' => $record->pivot->contacted_at,
                        'notes' => $record->pivot->notes,
                    ])
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(WorkerOrderStatus::class)
                            ->required()
                            ->live(),

                        DateTimePicker::make('contacted_at')
                            ->label('Contacted At')
                            ->native(false)
                            ->helperText('Leave empty to auto-set to now when status is Contacted or later.'),

                        Textarea::make('notes')
                            ->label('Call Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, $record) {
                        $status = $data['status'];

                        $contactedAt = $data['contacted_at'];
                        if (blank($contactedAt) && $status !== WorkerOrderStatus::Shortlisted->value) {
                            $contactedAt = now();
                        }

                        $record->pivot->update([
                            'status' => $status,
                            'contacted_at' => $contactedAt,
                            'contacted_by' => $status !== WorkerOrderStatus::Shortlisted->value
                                ? ($record->pivot->contacted_by ?? Auth::id())
                                : $record->pivot->contacted_by,
                            'notes' => $data['notes'],
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Status updated')
                            ->send();
                    }),

                DetachAction::make(),
            ])
            ->toolbarActions([
                BulkAction::make('exportProfileSheets')
                    ->label('Export Worker Profiles (PDF)')
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
                        return Excel::download(
                            new WorkersExport($records, $data['include_mobile']),
                            'worker-profiles-' . now()->format('Y-m-d-His') . '.xlsx'
                        );
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->headerActions([]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
}