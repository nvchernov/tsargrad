<?php require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_header.php'); ?>

<link href="/register.css" rel="stylesheet" type="text/css">

<div class="col-md-4 col-md-offset-1">
        <div>
            <h1 style="font-size:60px;">Царьград</h1>
            <p>В поисках власти...</p>
        </div>
        <form method="POST" action="/auth/login" class="form-horizontal">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h2 class="form-signin-heading">Вход в игру</h2>

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
                        <div class="col-lg-5 col-lg-offset-2">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="remember"> Запомнить меня
                                </label>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="checkbox">
                                <label>
                                    <a href="/password/email">Забыли пароль?</a>
                                </label>
                            </div>
                        </div>
                    </div>

                    <?php if (isset ($error_message)) { ?>
                        <div class="alert alert-danger" role="alert"> <strong>Ошибка!</strong> <? echo $error_message ?> </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <button type="submit" class="btn btn-md btn-primary btn-block">Вход в Средиземье</button>
                        </div>
                    </div>

                    <input type="hidden" name="_token" value="<? echo csrf_token() ?>">
                </div>
            </div>
        </form>
    </div>

<div class="col-md-5 col-md-offset-1">
    <div class="panel panel-default">
        <div class="panel-body">
            <h2>Новости</h2>
            <ul class="news">
                <?php foreach( $news as $entry ) : ?>
                    <li>
                        <h3><?php echo $entry->title; ?></h3>
                        <h4><?php echo $entry->date; ?></h4>
                        <p><?php echo str_limit($entry->text); ?></p>
                    </li>
                <?php endforeach; ?>
                <?php echo $news->render(); ?>
            </ul>
        </div>
    </div>
</div>


<? require_once($_SERVER['DOCUMENT_ROOT'].'/../resources/views/layout/master_footer.php'); ?>