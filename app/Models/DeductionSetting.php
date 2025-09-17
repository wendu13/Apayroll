<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeductionSetting extends Model
{
    protected $fillable = ['type', 'daily_rate', 'per_minute_rate'];
}

