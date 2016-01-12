<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_header.php'); ?>

<link href="/register.css" rel="stylesheet" type="text/css">

<div class="col-md-4 col-md-offset-4">
    <form method="POST" action="/password/reset" class="form-horizontal">
        <div class="panel panel-default" style="background-color: #ecf0f1; margin-top: 5%;">
            <div class="panel-body">
                <h2 class="form-signin-heading">Сброс пароля</h2>

                <?php if (isset ($error_message)) { ?>
                    <div class="alert alert-danger" role="alert"> <strong>Ошибка!</strong> <?php echo $error_message ?> </div>
                <?php } ?>

                <div class="form-group">
                  <label for="email" class="col-lg-2 control-label">Email</label>
                  <div class="col-lg-10">
                    <input class="form-control" id="email" name="email" placeholder="Email" type="email">
                  </div>
                </div>

                <div class="form-group">
                    <label for="password" class="col-lg-2 control-label">Пароль</label>
                    <div class="col-lg-10">
                        <input class="form-control" id="password" name="password" placeholder="Пароль" type="password">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="col-lg-2 control-label">Повторить пароль</label>
                    <div class="col-lg-10">
                        <input class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Повторить пароль" type="password">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <button type="submit" class="btn btn-md btn-primary btn-block">Сбросить</button>
                    </div>
                </div>

                <input type="hidden" name="_token" value="<?php echo csrf_token() ?>">
            </div>
        </div>
    </form>
</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_footer.php'); ?>