<?php
namespace App\Models;
use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
class AuthenticationLog extends Model
{
    
    /**
     * To disable auto-increment
     *
     * @var boolean
     */
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'ip_address', 'login_date',
    ];
}