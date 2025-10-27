<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentM extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments_m';

    protected $fillable = [
        'payment_id',
        'line_no',
        'payment_category',
        'bank_info',
        'amount',
        'apply_amount',
        'payment_type',
    ];

    public function paymentH(): BelongsTo
    {
        return $this->belongsTo(PaymentH::class, 'payment_id');
    }
}