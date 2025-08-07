<?php

namespace App\Enums;

enum IndustryType: int
{
    case MANUFACTURING = 1;
    case IT = 2;
    case MEDICAL = 3;

    public function label(): string
    {
        return match($this) {
            static::MANUFACTURING => '製造業',
            static::IT => 'IT',
            static::MEDICAL => '医療',
        };
    }
}
