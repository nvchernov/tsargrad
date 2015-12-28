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

    <meta charset="utf-8">
    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="/plugins/bootstrap3/css/bootstrap.min.css">
    <!-- Optional theme -->
    <link rel="stylesheet" href="/plugins/bootstrap3/css/bootstrap-theme.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="/css/style.css">
    <link href="/plugins/pace/themes/blue/pace-theme-flat-top.css" rel="stylesheet"/>

    <script type="text/javascript">
        // Для Pace.js
        window.paceOptions = {
            target: '.container'
        }
    </script>
    <script src="/plugins/pace/pace.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="/plugins/jquery/jquery-2.1.4.min.js"></script>
    <script src="/plugins/underscore/underscore-min.js"></script>
    <script src="/plugins/backbone/backbone-min.js"></script>
    <script src="/plugins/backbone.marionette/backbone.marionette.min.js"></script>
    <script src="/plugins/backbone.stickit/backbone.stickit.js"></script>
    <script src="/plugins/bootstrap3/js/bootstrap.min.js"></script>
    <script src="/plugins/bootstrap-slider/bootstrap-slider.min.js"></script>
    <script src="/plugins/noty/packaged/jquery.noty.packaged.min.js"></script>
    <script src="/plugins/noty/themes/bootstrap.js"></script>

    <script src="https://cdn.socket.io/socket.io-1.3.4.js"></script>

    <script src="/js/game.js"></script>
</head>
<body>

<header>
    <nav class="navbar navbar-default" style="border-radius: 0px;">

        <?php if (Auth::check()): ?>

            <!-- Темплейты для Marionette View... -->
            <script type="text/template" id="t-game-nav-res-li">
                <a href="#"><strong class="res-name"></strong>: <span class="res-count"></span></a>
            </script>

            <script type="text/template" id="t-game-nav-army-li">
                <a id="show-army-modal" href="#"><strong class="text-danger">Армия</strong>:
                    <span class="army-size"></span></a>
            </script>

            <script type="text/template" id="t-game-res">
                <span class="badge res-count"></span><span class="res-name"></span>
            </script>

            <script type="text/template" id="t-game-squads">
                <thead>
                <tr>
                    <th>Имя</th>
                    <th>Количество</th>
                    <th>Начало</th>
                    <th>Сражения</th>
                    <th>Окончание</th>
                </tr>
                </thead>
                <tbody></tbody>
            </script>

            <script type="text/template" id="t-game-squad">
                <td id="squad-name"></td>
                <td id="squad-size"></td>
                <td id="squad-date-begin"></td>
                <td id="squad-date-battle"></td>
                <td id="squad-date-end"></td>
            </script>

            <script type="text/template" id="t-game-enemy-castle">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="castleModalLabel">Вражеский замок <strong
                                id="enemy-castle-name"></strong></h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <form id="enemy-castle" class="form-horizontal">
                                    <fieldset class="col-sm-12">
                                        <legend>Приблизительные ресурсы замка</legend>
                                        <div id="enemy-resources" class="col-sm-12"></div>
                                    </fieldset>
                                    <fieldset id="army-crusade" class="col-sm-12">
                                        <legend>Подготовить нападение</legend>
                                        <div class="col-sm-12">
                                            <div class='form-group'>
                                                <label class="col-xs-4 control-label" style="text-align: left"
                                                       for="m-squad-name">Имя отряда</label>

                                                <div class="col-xs-8">
                                                    <input class="form-control" id="m-squad-name" type="text">
                                            <span id="m-squad-name-h"
                                                  class="help-block" style="display: none;">Не указано имя отряда</span>
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                <label class="col-xs-4" for="m-squad-size">Размер отряда</label>

                                                <div class="col-xs-4">
                                                    <input class="form-control" id="m-squad-size"
                                                           data-slider-id="sl-squad-size"
                                                           type="text" data-slider-step="1"
                                                           data-slider-value="1" data-slider-min="1">
                                                </div>
                                            </div>
                                            <div class='form-group has-error' style="display: none;">
                                                <span id="m-squad-error-h" class="col-xs-12 help-block"></span>
                                            </div>
                                            <div class='form-group'>
                                                <div class="col-xs-2">
                                                    <button id="m-crusade" type="button" class="btn btn-danger">
                                                        Напасть
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </script>

            <script type="text/template" id="t-game-army">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="armyModalLabel">Моя армия</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <form class="form-horizontal">
                                    <fieldset class="col-sm-12">
                                        <legend>Состояние армии</legend>
                                        <div class="col-sm-12">
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    <span id="my-army-level" class="badge"></span>
                                                    Уровень армии
                                                </li>
                                                <li class="list-group-item">
                                                    <span id="my-army-size" class="badge"></span>
                                                    Воинов в замке
                                                </li>
                                                <li class="list-group-item">
                                                    <span id="my-army-sizesquads" class="badge"></span>
                                                    Воинов в отрядах
                                                </li>
                                            </ul>
                                        </div>
                                    </fieldset>
                                    <fieldset class="col-sm-12">
                                        <legend>Покупка воинов</legend>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-xs-6">
                                                    <p>
                                                        Цена воина <span id="my-army-buy-price"
                                                                         class="badge"></span> <span
                                                            class="text-info">Еда</span> и <span id="my-army-buy-price"
                                                                                                 class="badge"></span> <span
                                                            class="text-warning">Дерево</span>
                                                    </p>
                                                </div>
                                                <div class="col-xs-4 col-md-offset-2">
                                                    <p>Стоимость <span id="m-army-cost" class="badge"></span></p>
                                                </div>
                                            </div>
                                            <div class='form-group'>
                                                <label class="col-xs-3" for="my-army-buy-size"
                                                       style="font-weight: 100">Количество</label>

                                                <div class="col-xs-6">
                                                    <input id="my-army-buy-size" data-slider-id="sl-army-buy-size"
                                                           type="text" data-slider-step="1" data-slider-value="1"
                                                           data-slider-min="1" data-slider-max="1000">
                                                </div>
                                                <div class="col-xs-2">
                                                    <button type="button" id="m-army-buy" class="btn btn-primary">
                                                        Купить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class='form-group' style="display: none;">
                                                <span id="m-army-result-buy" class="col-xs-12 help-block"></span>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset class="col-sm-12">
                                        <legend>Улучшение армии</legend>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div class="col-xs-9">
                                                    <p>Переход на <span id="my-army-level-up"
                                                                                   class="badge"></span> уровень стоит <span
                                                            id="my-army-upgrade-price" class="badge"></span> <span class="text-warning">Золото</span>
                                                    </p>
                                                </div>
                                                <div class="col-xs-2">
                                                    <button type="button" id="m-army-upgrade" class="btn btn-primary">
                                                        Улучшить
                                                    </button>
                                                </div>
                                            </div>
                                            <div class='form-group' style="display: none">
                                                <span id="m-army-result-upgrade" class="col-xs-12 help-block"></span>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <fieldset class="col-sm-12">
                                        <legend>Отряды</legend>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <div id="my-squads" class="col-xs-12"></div>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    </div>
                </div>
            </script>
            <!-- ... END Темплейты для Marionette View. -->

            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">
                        <?php echo Auth::user()->castle->name ?>
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="game-navbar">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false"><?php echo Auth::user()->name ?> <span
                                    class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?= Auth::user()->pathToProfile() ?>"><i class="glyphicon glyphicon-user"></i> Профиль</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="/game">Карта</a></li>
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
                // Базовые сущности...
                player.castle = new Models.Castle(<?= Auth::user()->castle->toJson() ?>);
                player.resources = new Models.Resources(_.defaults(<?= Auth::user()->castle->getResources()->toJson() ?>, defaultResources));
                player.army = new Models.Army(<?= Auth::user()->army->toJson() ?>);
                player.squads = new Models.Squads(<?= Auth::user()->army->squads->toJson() ?>);

                // Рендеринг навигации...
                $('#game-navbar').prepend((new Views.ResourcesNav({collection: player.resources})).render().el);
                $('#game-navbar > ul.resources-nav').append((new Views.ArmyLi({model: player.army})).render().el);
                $('#army-modal').append((new Views.Army({
                    model: player.army,
                    squads: player.squads
                })).render().el);
                $('#show-army-modal').click(function () {
                    $.get('game/armies/' + <?= Auth::user()->army->id ?>, function (resp) {
                        if (resp.success) {
                            player.army.set(resp.data.army);
                            player.squads.set(resp.data.squads);
                            $('#army-modal').modal();
                        }
                    }, 'json');
                });

                /////// Вебсокеты
                var socket = io.connect('http://localhost:8080', {reconnection: false});
                // Базовый channel...
                var channel = 'message/user/<?= Auth::user()->id ?>';

                // Все обработчики sub-событий.
                // главный канал.
                socket.on(channel, function (msg) {
                    noty({
                        theme: 'bootstrapTheme',
                        closeWith: ['button'],
                        layout: 'bottomRight',
                        text: msg
                    });
                });
                // CUD отряда.
                socket.on(channel + '/squad/update', function (model) {
                    var squad = player.squads.get(model.id);
                    squad.set(model);
                });
                socket.on(channel + '/squad/create', function (model) {
                    var squad = new Models.Squad(model);
                    player.squads.add(squad);
                });
                socket.on(channel + '/squad/delete', function (data) {
                    var squad = player.squads.get(data.id);
                    player.squads.remove(squad);
                });
                // Обновление армии.
                socket.on(channel + '/army/update', function (model) {
                    player.army.set(model);
                });
                // Обновление замка.
                socket.on(channel + '/castle/update', function (model) {
                    player.castle.set(model);
                });
                // Обновление ресурса.
                socket.on(channel + '/resource/update', function (model) {
                    var res = player.resources.get(model.name);
                    res.set(model);
                });
            </script>

        <?php else: ?>

            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/auth/login">Царьград</a>
                </div>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/auth/register">Регистрация</a></li>
                </ul>
            </div>

        <?php endif ?>
    </nav>
</header>