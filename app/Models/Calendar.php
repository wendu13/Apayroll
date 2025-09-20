<?php

// app/Models/Calendar.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    protected $table = 'calendar'; // If your table is named 'calendar' not 'calendars'

    protected $fillable = [
        'date',
        'name',
        'type',
    ];
}
