<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = ['number', 'title', 'status', 'payment', 'client', 'text'];
}
