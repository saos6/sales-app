<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateM extends Model
{
    use HasFactory;

    protected $table = 'estimate_m'; // Specify the table name

    protected $fillable = [
        'estimate_id',
        'line_no',
        'item_code',
        'item_name',
        'spec',
        'quantity',
        'unit',
        'unit_price',
        'amount',
        'tax_rate',
        'remarks',
        'deleted',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'amount' => 'decimal:2',
        'deleted' => 'boolean',
    ];

    // Relationship with EstimateH (many-to-one)
    public function estimate()
    {
        return $this->belongsTo(EstimateH::class, 'estimate_id', 'id');
    }
}