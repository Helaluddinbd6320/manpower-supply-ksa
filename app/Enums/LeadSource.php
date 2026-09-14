<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LeadSource: string implements HasLabel
{
    case VisitingCard = 'Visiting Card';
    case Internet = 'Internet';
    case Referral = 'Referral';
    case ColdCall = 'Cold Call';
    case Other = 'Other';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}