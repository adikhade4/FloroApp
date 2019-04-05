<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuthenticationLog extends Model
{
    protected $fillable = [
        'user_id', 'ip_address', 'login_date',
    ];
}
