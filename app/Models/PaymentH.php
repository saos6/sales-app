<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentH extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payments_h';

    protected $fillable = [
        'receipt_no',
        'receipt_date',
        'cust_id',
        'cust_name',
        'emp_id',
        'subject',
        'remarks',
        'total_amount',
        'status',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(PaymentM::class, 'payment_id');
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