<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 30.11.2015
 * Time: 23:16
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'resources';

    protected $fillable = ['name'];

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * Извлечь ресурс из БД если это возможно.
     *
     * @param $attr
     * @return Resource|null
     */
    public static function extract($attr)
    {
        $res = null;
        if ($attr instanceof static) {
            $res = $attr;
        } elseif (is_string($attr)) {
            $res = static::where(['name' => $attr])->first();
        } elseif (is_numeric($attr)) {
            $res = static::find($attr);
        }
        return $res;
    }

    /**
     * Get scores for the resource...
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function scores()
    {
        return $this->hasMany('App\Models\Score');
    }

    /**
     * Get rewards for the resource...
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rewards()
    {
        return $this->hasMany('App\Models\Reward');
    }

    /**
     * Get castles...
     * Many to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function castles()
    {
        return $this->belongsToMany('App\Models\Castle', 'scores', 'resource_id', 'castle_id')
            ->withTimestamps()
            ->withPivot('count');
    }

    /**
     * Получить все отряды...
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function squads()
    {
        return $this->belongsToMany('App\Models\Squad', 'rewards', 'resource_id', 'squad_id')
            ->withTimestamps()
            ->withPivot('count');
    }
}