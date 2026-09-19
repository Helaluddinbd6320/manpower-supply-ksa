<?php

namespace App\Filament\Resources\CandidateLeads\Pages;

use App\Filament\Resources\CandidateLeads\CandidateLeadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCandidateLead extends EditRecord
{
    protected static string $resource = CandidateLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
