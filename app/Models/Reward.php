<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 30.11.2015
 * Time: 23:13
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model, Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rewards';

    public function resource()
    {
        $this->belongsTo('App\Models\Resource');
    }

    public function squad()
    {
        return $this->belongsTo('App\Models\Squad');
    }
}