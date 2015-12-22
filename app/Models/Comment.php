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
}
