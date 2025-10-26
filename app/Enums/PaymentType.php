<?php

namespace App\Enums;

enum PaymentType: int
{
    case CASH = 1;
    case TRANSFER = 2;
    case CHECK = 3;
    case OFFSET = 4;
    case FEE = 5;
    case OTHER = 9;

    public function label(): string
    {
        return match($this) {
            static::CASH => '現金',
            static::TRANSFER => '振込',
            static::CHECK => '小切手',
            static::OFFSET => '相殺',
            static::FEE => '手数料',
            static::OTHER => 'その他',
        };
    }
}
