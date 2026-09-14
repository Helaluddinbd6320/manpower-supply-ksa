<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PlacementStatus: string implements HasLabel, HasColor
{
    case Active = 'active';
    case Completed = 'completed';
    case Terminated = 'terminated';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Completed => 'Completed',
            self::Terminated => 'Terminated',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Completed => 'gray',
            self::Terminated => 'danger',
        };
    }
}