<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>
<link rel="stylesheet" href="/css/blog-comment.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <form method="post" action="/user/profile/add_comment">
            <input name="comment_block_id" class="hidden" value="<?=$block->id?>"/>
            <input name="parent_comment_id" class="hidden parent-comment-id"/>
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <p>Комментарии</p>
                </div>

                <div class="col-lg-9 col-md-6">
                    <textarea name="text" class="form-control"></textarea>
                </div>

                <div class="col-lg-2 col-md-4" >
                    <div class="center-block" >
                        <input type="submit" class="btn btn-success  center-block" value="Добавить"/>
                    </div>
                    <div  class="comment-answer-block center-block hidden">
                        <p class="text-center">
                            <i>в ответ для</i>
                            &nbsp;
                            <a href="#" class="comment-to">janv</a>
                            &nbsp;
                            <a href="#" class="delete-comment-to"><i class="glyphicon glyphicon-remove"></i></a>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-body">
        <div class="blog-comment">
            <ul class="comments">
            <?php foreach( $comments as $comment ) { ?>
                <?php echo view('comments/comment', ['comment' => $comment]); ?>
            <?php } ?>
            </ul>
        </div>
    </div>
    <?php if ($page_count > 1):?>
    <div class="panel-footer clearfix" style="background-color: #fff">
        <nav class="pull-right">
            <ul class="pagination">
                <?php if ($page > 1) : ?>
                    <li>
                        <a href="/user/profile/<?=$user->id?>?page=<?=($page-1)?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for($i=1; $i <= $page_count; ++$i): ?>
                    <li class="<?=$i == $page ? 'active' : ''?>">
                        <a href="/user/profile/<?=$user->id?>?page=<?=$i?>">
                            <?=$i?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $page_count) : ?>
                    <li>
                        <a href="/user/profile/<?=$user->id?>?page=<?=($page+1)?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
