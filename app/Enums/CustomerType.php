<?php

namespace App\Enums;

enum CustomerType: int
{
    case CORPORATION = 1;
    case INDIVIDUAL = 2;

    public function label(): string
    {
        return match($this) {
            static::CORPORATION => '法人',
            static::INDIVIDUAL => '個人',
        };
    }
}
