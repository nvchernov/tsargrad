<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 30.11.2015
 * Time: 23:13
 */

namespace app\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rewards';

    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    public function resource()
    {
        $this->belongsTo('App\Models\Resource');
    }

    public function squad()
    {
        return $this->belongsTo('App\Models\Squad');
    }
}