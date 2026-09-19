<?php

namespace App\Filament\Resources\CandidateLeads;

use App\Filament\Resources\CandidateLeads\Pages\CreateCandidateLead;
use App\Filament\Resources\CandidateLeads\Pages\EditCandidateLead;
use App\Filament\Resources\CandidateLeads\Pages\ListCandidateLeads;
use App\Filament\Resources\CandidateLeads\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\CandidateLeads\Schemas\CandidateLeadForm;
use App\Filament\Resources\CandidateLeads\Tables\CandidateLeadsTable;
use App\Models\CandidateLead;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CandidateLeadResource extends Resource
{
    protected static ?string $model = CandidateLead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|UnitEnum|null $navigationGroup = 'Sales / Leads';

    protected static ?string $navigationLabel = 'Candidate Leads';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CandidateLeadForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CandidateLeadsTable::configure($table);
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
            'index' => ListCandidateLeads::route('/'),
            'create' => CreateCandidateLead::route('/create'),
            'edit' => EditCandidateLead::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereDate('next_follow_up_date', '<=', now())
            ->whereNotIn('status', ['Converted to Worker', 'Not Interested'])
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('view candidate leads') || $user?->can('manage candidate leads');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can('manage candidate leads') ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->can('manage candidate leads') ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->can('manage candidate leads') ?? false;
    }
}