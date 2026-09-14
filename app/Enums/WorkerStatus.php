<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum WorkerStatus: string implements HasLabel, HasColor
{
    case Available = 'available';
    case Shortlisted = 'shortlisted';
    case Interview = 'interview';
    case Selected = 'selected';
    case Placed = 'placed';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::Shortlisted => 'Shortlisted',
            self::Interview => 'Interview',
            self::Selected => 'Selected',
            self::Placed => 'Placed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Available => 'gray',
            self::Shortlisted => 'info',
            self::Interview => 'warning',
            self::Selected, self::Placed => 'success',
        };
    }
}