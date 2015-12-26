<? $user = $comment->user ?>
<? $parent_user = $comment->answerFor() ?>
<li class="clearfix comment" style="padding-left: <?=$comment->level()*7?>%">
    <? echo view('user/avatar', ['user' => $user])?>
    <input name="comment_id" class="hidden comment-id" value="<?=$comment->id?>">
    <div class="post-comments">
        <p class="meta">
            <?= $comment->created_at ?>
            <a href="/user/profile/<?=$user->id ?>" class="user-href"><?= $user->name ?></a> :
            <? if($parent_user != null) : ?>
                <i>для</i>&nbsp;
                <a href="/user/profile/<?=$parent_user->id?>">
                    <?=$parent_user->name?>
                </a>
            <? endif;?>
            <i class="pull-right">
                <a href="#" class="comment-reply">
                    <small>Ответить</small>
                </a>
            </i>
        </p>
        <p class="comment-content"><?= $comment->text ?></p>
    </div>
</li>
