<?php
/**
 * @autor: Козлов Дмитрий
 */
namespace App\Models;

use App\Exceptions\GameException;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';
    protected $dates = ['deleted_at', 'updated_at', 'created_at'];
    protected $fillable = array('user_id', 'text', 'comment_block_id', 'parent_comment_id');

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function level()
    {
        $level = substr_count($this->hierarchy,'-');
        return $level > 10 ? 10 : $level;
    }

    public function answerFor()
    {
        return $this->parent()->count() != 0 ?
            $this->parent()->get()->first()->user()->get()->first() :
            null;
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\Comment', 'parent_comment_id');
    }
}
