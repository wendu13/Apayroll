<?php

// app/Models/Hr.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Hr extends Authenticatable
{
    protected $fillable = [
        'employee_number',
        'position',
        'department',
        'first_name',
        'last_name',
        'email',
        'security_question',
        'security_answer',
        'password',
        'is_approved',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    
}

