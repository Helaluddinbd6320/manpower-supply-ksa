<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;

enum IqamaStatus: string implements HasLabel, HasColor, HasIcon
{
    case Valid = 'valid';
    case Expired = 'expired';
    case ValidWithHuroob = 'valid_with_huroob';
    case ExpiredWithHuroob = 'expired_with_huroob';
    case NoIqamaBorderNumber = 'no_iqama_border_number';

    public function getLabel(): string
    {
        return match ($this) {
            self::Valid => 'Valid',
            self::Expired => 'Expired',
            self::ValidWithHuroob => 'Valid (Huroob)',
            self::ExpiredWithHuroob => 'Expired (Huroob)',
            self::NoIqamaBorderNumber => 'No Iqama — Border Number',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Valid => 'success',
            self::Expired => 'warning',
            self::ValidWithHuroob => 'danger',
            self::ExpiredWithHuroob => 'danger',
            self::NoIqamaBorderNumber => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Valid => 'heroicon-o-check-circle',
            self::Expired => 'heroicon-o-clock',
            self::ValidWithHuroob, self::ExpiredWithHuroob => 'heroicon-o-exclamation-triangle',
            self::NoIqamaBorderNumber => 'heroicon-o-identification',
        };
    }
}