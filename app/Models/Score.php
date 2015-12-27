<?php
/**
 * Created by PhpStorm.
 * User: �����
 * Date: 30.11.2015
 * Time: 23:15
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'scores';

    public function resource()
    {
        return $this->belongsTo('App\Models\Resource');
    }

    public function castle()
    {
        return $this->belongsTo('App\Models\Castle');
    }
}