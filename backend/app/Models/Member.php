<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'call_sign',
        'age',
        'status',
        'role',
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

    protected function casts(): array
    {
        return [
            'skills' => 'array',
            'gear' => 'array',
            'connections' => 'array',
        ];
    }
}
