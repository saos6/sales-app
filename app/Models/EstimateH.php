<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // For UUID generation

class EstimateH extends Model
{
    use HasFactory;

    protected $table = 'estimate_h'; // Specify the table name

    protected $keyType = 'string'; // Specify that the primary key is a string (UUID)
    public $incrementing = false; // Disable auto-incrementing for UUID

    protected $fillable = [
        'estimate_no',
        'estimate_date',
        'valid_until',
        'cust_id',
        'cust_name',
        'cust_department',
        'cust_person',
        'emps_id',
        'subject',
        'subtotal',
        'tax',
        'total',
        'currency',
        'remarks',
        'status',
        'deleted',
    ];

    protected $casts = [
        'estimate_date' => 'date',
        'valid_until' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'deleted' => 'boolean',
    ];

    // Automatically generate UUID for new models
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    // Relationship with EstimateM (one-to-many)
    public function details()
    {
        return $this->hasMany(EstimateM::class, 'estimate_id', 'id');
    }

    // Relationship with Cust (assuming Cust model exists)
    public function customer()
    {
        return $this->belongsTo(Cust::class, 'cust_id', 'id');
    }

    // Relationship with Emp (assuming Emp model exists)
    public function employee()
    {
        return $this->belongsTo(Emp::class, 'emps_id', 'id');
    }
}