<?php

namespace App\Filament\Resources\QuotationTemplateBlocks;

use App\Filament\Resources\QuotationTemplateBlocks\Pages\CreateQuotationTemplateBlock;
use App\Filament\Resources\QuotationTemplateBlocks\Pages\EditQuotationTemplateBlock;
use App\Filament\Resources\QuotationTemplateBlocks\Pages\ListQuotationTemplateBlocks;
use App\Filament\Resources\QuotationTemplateBlocks\Schemas\QuotationTemplateBlockForm;
use App\Filament\Resources\QuotationTemplateBlocks\Tables\QuotationTemplateBlocksTable;
use App\Models\QuotationTemplateBlock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class QuotationTemplateBlockResource extends Resource
{
    protected static ?string $model = QuotationTemplateBlock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    protected static string|UnitEnum|null $navigationGroup = 'Quotations';

    protected static ?string $navigationLabel = 'Templates & Blocks';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return QuotationTemplateBlockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationTemplateBlocksTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuotationTemplateBlocks::route('/'),
            'create' => CreateQuotationTemplateBlock::route('/create'),
            'edit' => EditQuotationTemplateBlock::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }
}