<?php

namespace App\Enums;

enum CustomerRank: int
{
    case A = 1;
    case B = 2;
    case C = 3;

    public function label(): string
    {
        return match($this) {
            static::A => 'Aランク',
            static::B => 'Bランク',
            static::C => 'Cランク',
        };
    }
}
