<?php
/**
 * @autor: Козлов Дмитрий
 */
namespace App\Models;

use App\Exceptions\GameException;
use Illuminate\Database\Eloquent\Model;

class CommentBlock extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comment_blocks';
    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    /**
     * Добавляет комментарий в текущий блок
     * @param $user_id идентификатор пользователя
     * @param $text текст комментария
     * @param null $parent_comment_id идентификатор "родительского" комментария
     */
    public function addComment($user_id, $text, $parent_comment_id = null)
    {
        $comment = Comment::create([
            'user_id' => $user_id,
            'text' => $text,
            'parent_comment_id' => $parent_comment_id,
            'comment_block_id' => $this->id
        ]);
        $comment->save();
    }

    /**
     * @return Количество страниц комментариев
     */
    public function getPageCount()
    {
        return cell( $this->comments()->getEager()->count() / $this->getPerPage() );
    }

    /**
     * @param $page номер страницы
     * @return комментарии на странице
     */
    public function getPage($page)
    {
        return $this->comments()
            ->getEager()
            ->sortBy('hierarchy')
            ->splice($page*$this->getPerPage())
            ->take($this->getPerPage())
            ->all();
    }

    /**
     * Get all comments attacking the comment_block.
     * One to Many.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'comment_block_id');
    }
}
