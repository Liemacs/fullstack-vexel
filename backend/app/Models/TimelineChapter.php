<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimelineChapter extends Model
{
    protected $fillable = ['timeline_event_id', 'chapter', 'title', 'text', 'sort_order'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(TimelineEvent::class, 'timeline_event_id');
    }
}
