<?php

namespace App\Enums;

enum PaymentCategory: int
{
    case ACCOUNTS_RECEIVABLE = 1;
    case ADVANCE_PAYMENT = 2;
    case DEPOSIT = 3;
    case OTHER = 9;

    public function label(): string
    {
        return match($this) {
            static::ACCOUNTS_RECEIVABLE => '売掛金',
            static::ADVANCE_PAYMENT => '前受金',
            static::DEPOSIT => '手付金',
            static::OTHER => 'その他',
        };
    }
}
