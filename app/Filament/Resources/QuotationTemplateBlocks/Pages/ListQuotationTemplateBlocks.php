<?php

namespace App\Filament\Resources\QuotationTemplateBlocks\Pages;

use App\Filament\Resources\QuotationTemplateBlocks\QuotationTemplateBlockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotationTemplateBlocks extends ListRecords
{
    protected static string $resource = QuotationTemplateBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
