<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Nationality: string implements HasLabel
{
    case Bangladesh = 'bangladesh';
    case Philippines = 'philippines';
    case India = 'india';
    case Pakistan = 'pakistan';
    case Nepal = 'nepal';
    case SriLanka = 'sri_lanka';
    case Indonesia = 'indonesia';
    case Myanmar = 'myanmar';
    case Vietnam = 'vietnam';
    case Ethiopia = 'ethiopia';
    case Kenya = 'kenya';
    case Uganda = 'uganda';
    case Other = 'other';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Bangladesh => 'Bangladesh',
            self::Philippines => 'Philippines',
            self::India => 'India',
            self::Pakistan => 'Pakistan',
            self::Nepal => 'Nepal',
            self::SriLanka => 'Sri Lanka',
            self::Indonesia => 'Indonesia',
            self::Myanmar => 'Myanmar',
            self::Vietnam => 'Vietnam',
            self::Ethiopia => 'Ethiopia',
            self::Kenya => 'Kenya',
            self::Uganda => 'Uganda',
            self::Other => 'Other',
        };
    }
}