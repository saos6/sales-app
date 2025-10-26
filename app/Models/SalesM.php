<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesM extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales_m';

    protected $fillable = [
        'sales_id',
        'line_no',
        'item_code',
        'item_name',
        'spec',
        'quantity',
        'unit',
        'unit_price',
        'amount',
        'tax_rate',
        'tax_amount',
        'delivery_qty',
        'remarks',
    ];

    public function salesH(): BelongsTo
    {
        return $this->belongsTo(SalesH::class, 'sales_id');
    }
}
