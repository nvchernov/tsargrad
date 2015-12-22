<?php
/**
 * Created by PhpStorm.
 * User: Козлов Дмитрий
 * Date: 22.12.2015
 * Time: 19:46
 */

?>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/header.php'); ?>
<?php print_r( '<pre>'.$result.'</pre>' ); ?>
<div class="panel panel-info">
    <div class="panel-heading">
        <form method="post" action="comments/add">
            <input name="comment_block_id" class="hidden">
            <input name="parent_comment_id" class="hidden">
            <input name="user_id" class="hidden">
            <div class="row">
                <div class="col-lg-1 col-md-2">
                    <p>Комментарии</p>
                </div>

                <div class="col-lg-10 col-md-8">
                    <textarea name="comment_text" class="form-control"></textarea>
                </div>

                <div class="col-lg-1 col-md-2">
                    <input type="submit" class="btn btn-success btn-sm" value="Добавить">
                </div>
            </div>
        </form>
    </div>
    <div class="panel-body">
    </div>
</div>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/footer.php'); ?>
