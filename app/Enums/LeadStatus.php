<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasLabel, HasColor
{
    case New = 'New';
    case Contacted = 'Contacted';
    case Interested = 'Interested';
    case NotInterested = 'Not Interested';
    case Converted = 'Converted to Client';

    public function getLabel(): ?string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::New => 'gray',
            self::Contacted => 'info',
            self::Interested => 'warning',
            self::NotInterested => 'danger',
            self::Converted => 'success',
        };
    }
}