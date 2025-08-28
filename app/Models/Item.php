<?php

namespace App\Models;

use App\Enums\CategoryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_code',
        'item_name',
        'spec',
        'category_id',
        'unit',
        'standard_price',
        'cost_price',
        'tax_rate',
        'currency',
        'maker',
        'jan_code',
        'stock_qty',
        'reorder_point',
        'lead_time',
        'status',
        'remarks',
    ];

    protected $casts = [
        'category_id' => CategoryType::class,
    ];
}


