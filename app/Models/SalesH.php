<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesH extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sales_h';

    protected $fillable = [
        'sales_no',
        'sales_date',
        'posting_date',
        'invoice_id',
        'estimate_id',
        'cust_id',
        'cust_name',
        'cust_department',
        'cust_person',
        'emps_id',
        'subject',
        'subtotal',
        'tax',
        'total',
        'payment_status',
        'payment_date',
        'receipt_no',
        'remarks',
        'status',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(SalesM::class, 'sales_id');
    }

    public function cust(): BelongsTo
    {
        return $this->belongsTo(Cust::class);
    }

    public function emp(): BelongsTo
    {
        return $this->belongsTo(Emp::class, 'emps_id');
    }
}
