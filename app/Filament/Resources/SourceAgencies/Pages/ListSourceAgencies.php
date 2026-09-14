<?php

namespace App\Filament\Resources\SourceAgencies\Pages;

use App\Filament\Resources\SourceAgencies\SourceAgencyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSourceAgencies extends ListRecords
{
    protected static string $resource = SourceAgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}