<?php

namespace App\Filament\Resources\LandingPages;

use App\Filament\Resources\LandingPages\Pages\CreateLandingPage;
use App\Filament\Resources\LandingPages\Pages\EditLandingPage;
use App\Filament\Resources\LandingPages\Pages\ListLandingPages;
use App\Filament\Resources\LandingPages\Schemas\LandingPageForm;
use App\Filament\Resources\LandingPages\Tables\LandingPagesTable;
use App\Models\LandingPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LandingPageResource extends Resource
{
    protected static ?string $model = LandingPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAsiaAustralia;

    protected static string|UnitEnum|null $navigationGroup = 'Marketing';

    protected static ?string $navigationLabel = 'Landing Pages';

    protected static ?string $recordTitleAttribute = 'h1_heading';

    public static function form(Schema $schema): Schema
    {
        return LandingPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LandingPagesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLandingPages::route('/'),
            'create' => CreateLandingPage::route('/create'),
            'edit' => EditLandingPage::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return $user?->can('view leads') || $user?->hasRole('Super Admin');
        // ⚠️ নিচে নোট দেখুন — এখানে নতুন 'manage landing pages' permission ব্যবহার করা ভালো
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }
}