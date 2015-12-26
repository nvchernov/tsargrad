<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>
<link rel="stylesheet" href="/css/blog-comment.css">
<div class="panel panel-default">
    <div class="panel-heading">
        <form method="post" action="/comments/add">
            <input name="comment_block_id" class="hidden" value="<?=$block->id?>"/>
            <input name="parent_comment_id" class="hidden"/>
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <p>Комментарии</p>
                </div>

                <div class="col-lg-10 col-md-8">
                    <textarea name="text" class="form-control"></textarea>
                </div>

                <div class="col-lg-1 col-md-2">
                    <input type="submit" class="btn btn-success btn-sm" value="Добавить"/>
                </div>
            </div>
        </form>
    </div>
    <div class="panel-body">
        <div class="blog-comment">
            <ul class="comments">
            <? foreach( $comments as $comment ) { ?>
                <? echo view('comments/comment', ['comment' => $comment]); ?>
            <? } ?>
            </ul>
        </div>
    </div>
    <? if ($page_count > 1):?>
    <div class="panel-footer clearfix" style="background-color: #fff">
        <nav class="pull-right">
            <ul class="pagination">
                <? if ($page > 1) : ?>
                    <li>
                        <a href="/comments/<?=$block->id.'/'.($page-1)?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <? endif; ?>
                <? for($i=1; $i <= $page_count; ++$i): ?>
                    <li class="<?=$i == $page ? 'active' : ''?>">
                        <a href="/comments/<?=$block->id.'/'.$i?>">
                            <?=$i?>
                        </a>
                    </li>
                <? endfor; ?>

                <? if ($page < $page_count) : ?>
                    <li>
                        <a href="/comments/<?=$block->id.'/'.($page+1)?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <? endif; ?>
            </ul>
        </nav>
    </div>
    <? endif; ?>
</div>
<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
