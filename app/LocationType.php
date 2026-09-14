<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LocationType: string implements HasLabel
{
    case PreDeparture = 'pre_departure';
    case InSaudiArabia = 'in_saudi_arabia';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PreDeparture => 'Pre-departure (In Bangladesh)',
            self::InSaudiArabia => 'In Saudi Arabia',
        };
    }
}