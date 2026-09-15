<?php

namespace App\Filament\Resources\Agents;

use App\Filament\Resources\Agents\Pages\CreateAgent;
use App\Filament\Resources\Agents\Pages\EditAgent;
use App\Filament\Resources\Agents\Pages\ListAgents;
use App\Filament\Resources\Agents\Schemas\AgentForm;
use App\Filament\Resources\Agents\Tables\AgentsTable;
use App\Models\Agent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\Agents\RelationManagers\FollowUpsRelationManager;



class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Agents';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return AgentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgents::route('/'),
            'create' => CreateAgent::route('/create'),
            'edit' => EditAgent::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            FollowUpsRelationManager::class,
        ];
    }
}
