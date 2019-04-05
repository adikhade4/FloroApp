<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'field_name','new_value', 'old_value', 'modified_value', 'modified_by',
    ];
}
