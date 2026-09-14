<?php

namespace App\Filament\Resources\Placements\Pages;

use App\Filament\Resources\Placements\PlacementResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePlacement extends CreateRecord
{
    protected static string $resource = PlacementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['entered_by'] = auth()->id();

        return $data;
    }
}