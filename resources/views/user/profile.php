<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_header.php'); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-11 col-md-offset-1">
            <h2>Редактирование профиля</h2>

            <?php if (isset($message)) { ?>
                <?php if ($is_error) { ?>
                    <div class="alert alert-danger" role="alert"> <strong>Ошибка!</strong> <?php echo $message ?> </div>
                <?php } ?>

                <?php if (!$is_error) { ?>
                    <div class="alert alert-info" role="alert"> <strong>Готово!</strong> <?php echo $message ?> </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="row form-horizontal">
        <!--<form method="POST" action="/user/update" class="form-horizontal">-->

            <div class="row">
                <div class="col-md-3 col-md-offset-1">
                    <div class="row" style="max-height: 500px;">

                        <div class="col-md-8 col-md-offset-2" style="position: absolute;">
                            <img id="avatar_flag" src="<?php echo $flag_url ?>" style="height: 262px; width: 262px;">
                            <input id="flag_id" name="flag_id" placeholder="Имя полководца" type="hidden" value="<?php echo $avatar->flag_id ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-2 text-center">
                                <button id="avatar_top_prev" type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </button>
                            </div>
                            <div class="col-md-8">
                                <img id="avatar_top" src="<?php echo $hair_url ?>" style="width: 100%;">
                                <input id="hair_id" name="hair_id" placeholder="Имя полководца" type="hidden" value="<?php echo $avatar->hair_id ?>">
                            </div>
                            <div class="col-md-2 text-center">
                                <button id="avatar_top_next" type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 text-center">
                                <button id="avatar_middle_prev" type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </button>
                            </div>
                            <div class="col-md-8">
                                <img id="avatar_middle" src="<?php echo $mustache_url ?>" style="width: 100%;">
                                <input id="mustache_id" name="mustache_id" placeholder="Имя полководца" type="hidden" value="<?php echo $avatar->mustache_id ?>">
                            </div>
                            <div class="col-md-2 text-center">
                                <button id="avatar_middle_next" type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2 text-center">
                                <button id="avatar_bottom_prev" type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i>
                                </button>
                            </div>
                            <div class="col-md-8">
                                <img id="avatar_bottom" src="<?php echo $amulet_url ?>" style="width: 100%;">
                                <input id="amulet_id" name="amulet_id" placeholder="Имя полководца" type="hidden" value="<?php echo $avatar->amulet_id ?>">
                            </div>
                            <div class="col-md-2 text-center">
                                <button id="avatar_bottom_next" type="button" class="btn btn-primary center-block" style="margin-top: 50%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <button id="flag_prev" type="button" class="btn btn-xs btn-primary center-block" style="margin-top: 5%;">
                                    <i class="glyphicon glyphicon-chevron-left"></i> Предыдущий флаг
                                </button>
                            </div>

                            <div class="col-md-6">
                                <button id="flag_next" type="button" class="btn btn-xs btn-primary center-block" style="margin-top: 5%;">
                                    <i class="glyphicon glyphicon-chevron-right"></i> Следующий флаг
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name" class="col-lg-3 control-label">Имя полководца</label>
                        <div class="col-lg-9">
                            <input class="form-control" id="name" name="name" placeholder="Имя полководца" type="text" value="<?php echo $user->name ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="castle_name" class="col-lg-3 control-label">Наименование замка</label>
                        <div class="col-lg-9">
                            <input class="form-control" id="castle_name" name="castle_name" placeholder="Наименование замка" type="text" value="<?php echo $user->castle_name ?>">
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">

            <?php if ($user->id === Auth::user()->id): ?>
                <div class="row">
                    <button id="save_btn" type="submit" class="btn btn-primary center-block">Сохранить</button>
                </div>
            <?php endif; ?>

        <!--</div>-->
        <br/>
        <div class="row">
            <div class="col-lg-offset-1 col-lg-10">
                <?php echo view('comments/comments_block', [
                    'user' => $user,
                    'block' => $block,
                    'comments' => $comments,
                    'page_count' => $page_count,
                    'page' => $page
                ]); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $( document ).ready(function() {
        var change_avatar_partial = function(position, has_next, hidden_iput_id) {
            $.ajax({
                type: 'POST',
                url: '/avatar/get_partial',
                data: {
                    position: position,
                    has_next: has_next,
                    img_id: $(hidden_iput_id).val()
                },
                success: function(data) {
                    $('#avatar_' + position).attr('src', data['url']);
                    $(hidden_iput_id).val(data['id']);
                }
            });
        };

        $('#avatar_top_prev').click(function() {
            change_avatar_partial('top', false, '#hair_id');
        });

        $('#avatar_top_next').click(function() {
            change_avatar_partial('top', true, '#hair_id');
        });

        $('#avatar_middle_prev').click(function() {
            change_avatar_partial('middle', false, '#mustache_id');
        });

        $('#avatar_middle_next').click(function() {
            change_avatar_partial('middle', true, '#mustache_id');
        });

        $('#avatar_bottom_prev').click(function() {
            change_avatar_partial('bottom', false, '#amulet_id');
        });

        $('#avatar_bottom_next').click(function() {
            change_avatar_partial('bottom', true, '#amulet_id');
        });

        $('#flag_prev').click(function() {
            change_avatar_partial('flag', false, '#flag_id');
        });

        $('#flag_next').click(function() {
            change_avatar_partial('flag', true, '#flag_id');
        });

        $('#save_btn').click(function() {
            $.ajax({
                type: 'POST',
                url: '/user/update',
                data: {
                    flag_id: $('#flag_id').val(),
                    hair_id: $('#hair_id').val(),
                    mustache_id: $('#mustache_id').val(),
                    amulet_id: $('#amulet_id').val(),
                    name: $('#name').val(),
                    castle_name: $('#castle_name').val()
                },
                success: function(data) {
                    alert("Профиль успешно обновлен");
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    alert("Не удалось обновить профиль. Попробуйте еще раз или обратитесь к разработчикам.");
                }
            });
        });
    });
</script>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_footer.php'); ?>