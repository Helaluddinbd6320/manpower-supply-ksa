<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SourceAgencyStatus: string implements HasLabel, HasColor
{
    case New = 'New';
    case Contacted = 'Contacted';
    case ActivePartner = 'Active Partner';
    case NotInterested = 'Not Interested';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'gray',
            self::Contacted => 'info',
            self::ActivePartner => 'success',
            self::NotInterested => 'danger',
        };
    }
}