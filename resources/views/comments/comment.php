<li class="clearfix">
    <img src="http://bootdey.com/img/Content/user_1.jpg" class="avatar" alt="">
    <div class="post-comments">
        <p class="meta">
            <?= $comment->created_at ?>
            <a href="#"><?= $comment->user->name ?></a> :
            <i class="pull-right">
                <a href="#" class="comment-reply">
                    <small>Ответить</small>
                </a>
            </i>
        </p>
        <p><?= $comment->text ?></p>
    </div>
</li>
