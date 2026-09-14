<?php

namespace App\Filament\Resources\JobOrders\Pages;

use App\Filament\Resources\JobOrders\JobOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJobOrder extends CreateRecord
{
    protected static string $resource = JobOrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['entered_by'] = auth()->id();

        return $data;
    }
}