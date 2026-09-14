<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WorkerOrderStatus: string implements HasLabel, HasColor
{
    case Shortlisted = 'shortlisted';
    case Contacted = 'contacted';
    case Interested = 'interested';
    case NotInterested = 'not_interested';
    case Confirmed = 'confirmed';
    case RejectedByCompany = 'rejected_by_company';

    public function getLabel(): string
    {
        return match ($this) {
            self::Shortlisted => 'Shortlisted',
            self::Contacted => 'Contacted',
            self::Interested => 'Interested',
            self::NotInterested => 'Not Interested',
            self::Confirmed => 'Confirmed',
            self::RejectedByCompany => 'Rejected by Company',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Shortlisted => 'gray',
            self::Contacted => 'info',
            self::Interested => 'warning',
            self::NotInterested => 'danger',
            self::Confirmed => 'success',
            self::RejectedByCompany => 'danger',
        };
    }
}