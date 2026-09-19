<?php

namespace App\Filament\Resources\CandidateLeads\Pages;

use App\Filament\Resources\CandidateLeads\CandidateLeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCandidateLeads extends ListRecords
{
    protected static string $resource = CandidateLeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
