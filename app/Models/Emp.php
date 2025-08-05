<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emp extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'birthday',
        'depts_id',
    ];

    /**
     * Get the department for the employee.
     */
    public function dept(): BelongsTo
    {
        return $this->belongsTo(Dept::class, 'depts_id');
    }
}