<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Avatar extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'avatars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mustache_id', 'amulet_id', 'hair_id', 'flag_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function amulet()
    {
        return $this->hasOne('App\Models\Amulet');
    }

    public function mustache()
    {
        return $this->hasOne('App\Models\Mustache');
    }

    public function hair()
    {
        return $this->hasOne('App\Models\Hair');
    }

    public function flag()
    {
        return $this->hasOne('App\Models\Flag');
    }
}
