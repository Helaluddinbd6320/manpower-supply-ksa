<?php

namespace App\Filament\Resources\QuotationTemplateBlocks\Pages;

use App\Filament\Resources\QuotationTemplateBlocks\QuotationTemplateBlockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditQuotationTemplateBlock extends EditRecord
{
    protected static string $resource = QuotationTemplateBlockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
