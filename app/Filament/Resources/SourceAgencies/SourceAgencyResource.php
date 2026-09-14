<?php

namespace App\Filament\Resources\SourceAgencies;

use App\Filament\Resources\SourceAgencies\Pages\CreateSourceAgency;
use App\Filament\Resources\SourceAgencies\Pages\EditSourceAgency;
use App\Filament\Resources\SourceAgencies\Pages\ListSourceAgencies;
use App\Filament\Resources\SourceAgencies\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\SourceAgencies\Schemas\SourceAgencyForm;
use App\Filament\Resources\SourceAgencies\Tables\SourceAgenciesTable;
use App\Models\SourceAgency;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SourceAgencyResource extends Resource
{
    protected static ?string $model = SourceAgency::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedGlobeAlt;

    protected static string | UnitEnum | null $navigationGroup = 'Sourcing / Agencies';

    protected static ?string $navigationLabel = 'Source Agencies';

    protected static ?string $recordTitleAttribute = 'agency_name';

    public static function form(Schema $schema): Schema
    {
        return SourceAgencyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SourceAgenciesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            FollowUpsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSourceAgencies::route('/'),
            'create' => CreateSourceAgency::route('/create'),
            'edit' => EditSourceAgency::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereDate('next_follow_up_date', '<=', now())
            ->whereNotIn('status', ['Active Partner', 'Not Interested'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}