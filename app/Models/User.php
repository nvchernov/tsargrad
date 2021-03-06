<?php

namespace App\Models;

use App\Exceptions\GameException;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'castle_name', 'email', 'password', 'avatar_id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    public static function boot()
    {
        parent::boot();

        static::created(function(User $user)
        {
            $castle = $user->castle()->create(['name' => $user->caste_name ?: $user->name]);

            $cb = CommentBlock::create();
            $cb->save();
            $user->commentBlock()->associate($cb);
            $user->save();

            // Задать позицию на карте и армию по-умолчанию.
            $location = Location::freeRandom();
            if (is_null($location)) { throw new GameException('Нельзя добавить новый замок. Все поле уже занято.'); }
            $location->castle()->associate($castle);
            $location->save();

            $castle->army()->create(['name' => "{$castle->name}'s army", 'size' => 15, 'level' => 1]);
            
            // Создаем сооружения
            $castle->createBuildings();
            
            // Инициализировать ресурсы в замке
            $castle->initResources();
        });

        static::updated(function(User $user)
        {
            $castle = $user->castle;
            $castle->name = $user->castle_name;
            $castle->save();
        });
    }

    /**
     * Get castle.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function castle()
    {
        return $this->hasOne('App\Models\Castle');
    }

    public function commentBlock()
    {
        return $this->belongsTo('App\Models\CommentBlock','comment_block_id');
    }

    public function __get($key)
    {
        // Получить армию.
        if ($key == 'army') {
            return $this->castle ? $this->castle->army : null;
        }

        return parent::__get($key);
    }

    public function avatar()
    {
        return $this->hasOne('App\Models\Avatar');
    }

    public function lastPveAttack()
    {
        return $this->pveEnemyAttacks()->orderBy('id', 'desc')->first();
    }

    public function pveEnemyAttacks()
    {
        return $this->hasMany('App\Models\PveEnemyAttack');
    }

    public function pathToProfile()
    {
        return '/user/profile/' . $this->id;
    }
}
