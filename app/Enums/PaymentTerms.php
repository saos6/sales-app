<?php

namespace App\Enums;

enum PaymentTerms: int
{
    case DUE_20TH_NEXT_MONTH = 1;
    case DUE_END_OF_NEXT_MONTH = 2;

    public function label(): string
    {
        return match($this) {
            static::DUE_20TH_NEXT_MONTH => '20日締め翌月末払い',
            static::DUE_END_OF_NEXT_MONTH => '月末締め翌月末払い',
        };
    }
}
