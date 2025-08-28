<?php

namespace App\Enums;

enum CategoryType: int
{
    case BIKE = 1;
    case PARTS = 2;

    public function label(): string
    {
        return match($this) {
            static::BIKE => 'バイク',
            static::PARTS => '部品',
        };
    }
}


