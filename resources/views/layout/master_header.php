<?php
/**
 * Created by PhpStorm.
 * User: Nikolay
 * Date: 11.09.2015
 * Time: 15:33
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Царьград</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/plugins/bootstrap3/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="/plugins/bootstrap3/css/bootstrap-theme.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="/css/style.css">
    <link href="/plugins/pace/themes/blue/pace-theme-flat-top.css" rel="stylesheet" />

    <!-- Latest compiled and minified JavaScript -->
    <script src="/plugins/jquery/jquery-2.1.4.min.js"></script>
    <script src="/plugins/bootstrap3/js/bootstrap.min.js"></script>
    <script src="/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
    <script src="/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
    <script src="/plugins/noty/themes/bootstrap.js"></script>
    <script type="text/javascript">
        // Для Pace.js
        window.paceOptions = {
            target: '.container'
        }
    </script>
    <script src="/plugins/pace/pace.min.js"></script>
</head>
<body>

<header>
    <nav class="navbar navbar-default" style="border-radius: 0px;">

        <? if (Auth::check()): ?>

            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                        <? echo Auth::user()->castle->name ?>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="#"><strong class="text-warning">Золото</strong>: <span
                                    class="my-gold"><?= Auth::user()->castle->getResources('gold') ?: 0 ?></span></a>
                        </li>
                        <li><a href="#"><strong class="text-success">Дерево</strong>: <span
                                    class="my-wood"><?= Auth::user()->castle->getResources('wood') ?: 0 ?></span></a>
                        </li>
                        <li><a href="#"><strong class="text-info">Еда</strong>: <span
                                    class="my-food"><?= Auth::user()->castle->getResources('food') ?: 0 ?></span></a>
                        </li>
                        <li><a href="#" id="show-modal-army"><strong class="text-danger">Армия</strong>: <span
                                    class="my-army"><?= Auth::user()->army->size ?></span></a>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false"><? echo Auth::user()->name ?> <span
                                    class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="/user/profile"><i class="glyphicon glyphicon-user"></i> Профиль</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#">Карта</a></li>
                                <li><a href="#">Новости</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="/auth/logout"><i class="glyphicon glyphicon-log-out"></i> Покинуть
                                        Средиземье</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="army-modal" tabindex="-1" role="dialog"></div>

            <script type="text/javascript">
                $('#show-modal-army').click(function () {
                    $.get('game/armies/' + <?= Auth::user()->army->id ?>, function (data) {
                        $mod = $('#army-modal');
                        $mod.html(data);
                        $mod.modal();
                    }, 'html');
                });
            </script>

        <? else: ?>

            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/auth/login">Царьград</a>
                </div>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/auth/register">Регистрация</a></li>
                </ul>
            </div>

        <? endif ?>
    </nav>
</header>