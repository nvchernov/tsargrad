<? require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_header.php'); ?>

<script type="text/javascript">
    //TODO
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11 col-md-offset-1">
            <h2>Редактирование профиля</h2>

            <? if (isset ($error_message)) { ?>
                <div class="alert alert-danger" role="alert"> <strong>Ошибка!</strong> <? echo $error_message ?> </div>
            <? } ?>

            <? if (isset ($notice_message)) { ?>
                <div class="alert alert-info" role="alert"> <strong>Готово!</strong> <? echo $notice_message ?> </div>
            <? } ?>
        </div>
    </div>

    <div class="row">
        <form method="POST" action="/user/update" class="form-horizontal">

            <div class="row">
                <div class="col-md-3 col-md-offset-1">
                    <div class="row" style="max-height: 500px;">
                        <div class="row">
                            <div class="col-md-2 text-center">
                                <button type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </button>
                            </div>
                            <div class="col-md-8">
                                <img src="\images\default_avatar\4430813_01.png" style="width: 100%;">
                            </div>
                            <div class="col-md-2 text-center">
                                <button type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 text-center">
                                <button type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </button>
                            </div>
                            <div class="col-md-8">
                                <img src="\images\default_avatar\4430813_02.gif" style="width: 100%;">
                            </div>
                            <div class="col-md-2 text-center">
                                <button type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 text-center">
                                <button type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </button>
                            </div>
                            <div class="col-md-8">
                                <img src="\images\default_avatar\4430813_03.gif" style="width: 100%;">
                            </div>
                            <div class="col-md-2 text-center">
                                <button type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-lg-3 control-label">Имя полководца</label>
                        <div class="col-lg-9">
                            <input class="form-control" id="name" name="name" placeholder="Имя полководца" type="text" value="<? echo $user->name ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="castle_name" class="col-lg-3 control-label">Наименование замка</label>
                        <div class="col-lg-9">
                            <input class="form-control" id="castle_name" name="castle_name" placeholder="Наименование замка" type="text" value="<? echo $user->castle_name ?>">
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="_token" value="<? echo csrf_token() ?>">

            <div class="row">
                <button type="submit" class="btn btn-primary center-block">Сохранить</button>
            </div>

        </form>
        <br/>
        <div class="row">
            <div class="col-lg-offset-1 col-lg-10">
                <? echo view('comments/comments_block', [
                    'block' => $block,
                    'comments' => $comments,
                    'page_count' => $page_count,
                    'page' => $page
                ]);?>
            </div>
        </div>
    </div>
</div>

<? require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_footer.php'); ?>