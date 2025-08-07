<?php

namespace App\Models;

use App\Enums\CustomerRank;
use App\Enums\CustomerType;
use App\Enums\IndustryType;
use App\Enums\PaymentTerms;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cust extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'name_kana',
        'department_name',
        'contact_person_name',
        'postal_code',
        'prefecture',
        'address_line',
        'tel',
        'fax',
        'email',
        'website_url',
        'customer_type',
        'industry_type',
        'first_trade_date',
        'customer_rank',
        'payment_terms',
        'depts_id',
        'emps_id',
        'remarks',
    ];

    protected $casts = [
        'customer_type' => CustomerType::class,
        'industry_type' => IndustryType::class,
        'customer_rank' => CustomerRank::class,
        'payment_terms' => PaymentTerms::class,
        'first_trade_date' => 'date',
    ];

    public function dept()
    {
        return $this->belongsTo(Dept::class, 'depts_id');
    }

    public function emp()
    {
        return $this->belongsTo(Emp::class, 'emps_id');
    }
}