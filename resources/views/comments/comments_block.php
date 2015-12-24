<link rel="stylesheet" href="/plugins/css/blog-comment.css">
<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <form method="post" action="comments/add">
            <input name="comment_block_id" class="hidden"/>
            <input name="parent_comment_id" class="hidden"/>
            <input name="user_id" class="hidden"/>
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <p>Комментарии</p>
                </div>

                <div class="col-lg-10 col-md-8">
                    <textarea name="comment_text" class="form-control"></textarea>
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
            <? foreach( $comment as $comments ) { ?>
                <? require_once($_SERVER['DOCUMENT_ROOT'] . '/resources/comments/comment.php'); ?>
            <? } ?>
            </ul>
        </div>
    </div>
</div>
<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
