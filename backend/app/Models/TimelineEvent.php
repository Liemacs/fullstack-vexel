<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimelineEvent extends Model
{
    protected $fillable = ['year', 'title', 'text', 'sort_order'];

    public function chapters(): HasMany
    {
        return $this->hasMany(TimelineChapter::class)->orderBy('sort_order');
    }
}
