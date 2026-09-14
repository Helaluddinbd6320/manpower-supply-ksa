<?php

namespace App\Filament\Resources\Workers;

use App\Filament\Resources\Workers\Pages\CreateWorker;
use App\Filament\Resources\Workers\Pages\EditWorker;
use App\Filament\Resources\Workers\Pages\ListWorkers;
use App\Filament\Resources\Workers\Schemas\WorkerForm;
use App\Filament\Resources\Workers\Tables\WorkersTable;
use App\Models\Worker;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::OutlinedUsers;

    protected static string | UnitEnum | null $navigationGroup = 'Worker Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Workers';

    protected static ?string $modelLabel = 'Worker';

    protected static ?string $pluralModelLabel = 'Workers';

    // Worker ID (CHI-YYYY-XXXX) দিয়ে গ্লোবাল সার্চ করা যাবে
    protected static ?string $recordTitleAttribute = 'worker_id';

    public static function form(Schema $schema): Schema
    {
        return WorkerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkersTable::configure($table);
    }

    public static function getRelations(): array
{
    return [
        RelationManagers\DocumentsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => ListWorkers::route('/'),
            'create' => CreateWorker::route('/create'),
            'edit' => EditWorker::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['jobCategories']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('employment_status', 'free_available')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}