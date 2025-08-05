<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dept extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
    ];

    /**
     * Get the parent for the dept.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Dept::class, 'parent_id');
    }

    /**
     * Get the children for the dept.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Dept::class, 'parent_id');
    }
}
