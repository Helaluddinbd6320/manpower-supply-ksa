<?php

namespace App\Filament\Resources\SourceAgencies\Pages;

use App\Filament\Resources\SourceAgencies\SourceAgencyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSourceAgency extends CreateRecord
{
    protected static string $resource = SourceAgencyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['entered_by'] = auth()->id();

        return $data;
    }
}