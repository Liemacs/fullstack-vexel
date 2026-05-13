<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Position extends Model
{
    protected $fillable = ['name', 'image', 'sort_order'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
