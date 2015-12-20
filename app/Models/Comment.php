<?php
/**
 * @autor: Козлов Дмитрий
 */
namespace App\Models;

use App\Exceptions\GameException;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comment_blocks';
    protected $dates = ['deleted_at', 'updated_at', 'created_at'];
}
