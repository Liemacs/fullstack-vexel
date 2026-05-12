<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapPoint extends Model
{
    protected $fillable = ['public_id', 'name', 'type', 'x', 'y', 'risk', 'text'];
}
