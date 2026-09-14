<?php

namespace App\Filament\Resources\SourceAgencies\Pages;

use App\Filament\Resources\SourceAgencies\SourceAgencyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSourceAgency extends EditRecord
{
    protected static string $resource = SourceAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}