<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SSSBracket extends Model
{
    use HasFactory;

    protected $table = 'sss_brackets'; // explicitly set table name

    protected $fillable = ['from', 'to', 'er', 'ee', 'total', 'others'];
}
