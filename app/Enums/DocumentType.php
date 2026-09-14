<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum DocumentType: string implements HasLabel
{
    case Cv = 'cv';
    case Photo = 'photo';
    case Passport = 'passport';
    case Iqama = 'iqama';
    case Certificate = 'certificate';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cv => 'CV',
            self::Photo => 'Photo',
            self::Passport => 'Passport',
            self::Iqama => 'Iqama',
            self::Certificate => 'Certificate',
        };
    }

    /**
     * Types that should only ever have one "current" document per worker
     * (most recent row wins). Certificate is the only multi-file type.
     */
    public static function singleInstanceTypes(): array
    {
        return [self::Cv, self::Photo, self::Passport, self::Iqama];
    }
}