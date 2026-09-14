<?php

namespace App\Filament\Resources\Placements;

use App\Filament\Resources\Placements\Pages\CreatePlacement;
use App\Filament\Resources\Placements\Pages\EditPlacement;
use App\Filament\Resources\Placements\Pages\ListPlacements;
use App\Filament\Resources\Placements\Schemas\PlacementForm;
use App\Filament\Resources\Placements\Tables\PlacementsTable;
use App\Models\Placement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Placements\RelationManagers\MonthlyRecordsRelationManager;


class PlacementResource extends Resource
{
    protected static ?string $model = Placement::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string | BackedEnum | null $activeNavigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $recordTitleAttribute = 'client_company_name';

    public static function form(Schema $schema): Schema
    {
        return PlacementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PlacementsTable::configure($table);
    }

    public static function getRelations(): array
{
    return [
        MonthlyRecordsRelationManager::class,
    ];
}

    public static function getPages(): array
    {
        return [
            'index' => ListPlacements::route('/'),
            'create' => CreatePlacement::route('/create'),
            'edit' => EditPlacement::route('/{record}/edit'),
        ];
    }
}