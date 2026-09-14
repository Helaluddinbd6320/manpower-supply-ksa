<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LanguageProficiency: string implements HasLabel, HasColor
{
    case None = 'none';
    case Basic = 'basic';
    case Intermediate = 'intermediate';
    case Fluent = 'fluent';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'None',
            self::Basic => 'Basic',
            self::Intermediate => 'Intermediate',
            self::Fluent => 'Fluent',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::None => 'gray',
            self::Basic => 'warning',
            self::Intermediate => 'info',
            self::Fluent => 'success',
        };
    }
}