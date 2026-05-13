<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'slug',
        'nickname',
        'password',
        'name',
        'call_sign',
        'age',
        'status',
        'role',
        'position_id',
        'specialization',
        'image',
        'quote',
        'bio',
        'skills',
        'gear',
        'character',
        'vexel_history',
        'connections',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'gear' => 'array',
            'connections' => 'array',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function accessTokens(): HasMany
    {
        return $this->hasMany(MemberAccessToken::class);
    }
}
