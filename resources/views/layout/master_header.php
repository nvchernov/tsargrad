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
    <title>Царь град</title>

    <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <!-- Optional theme -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/3.3.6/flatly/bootstrap.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</head>
<body>

<header>
    <nav class="navbar navbar-default" style="border-radius: 0px;">

        <? if (Auth::check()) { ?>

        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <? echo Auth::user()->castle_name ?>
                </a>
            </div>

            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="#">Золото: <span>(current)</span></a></li>
                    <li><a href="#">Еда: <span>(current)</span></a></li>
                    <li><a href="#">Железо: <span>(current)</span></a></li>
                    <li>
                        <a href="#" data-toggle="modal" data-target="#myModal">Армия: <span>(current)</span>
                        </a>
                        <div class="progress">
                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%;">50%</div>
                        </div>
                    </li>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><? echo Auth::user()->name ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="/user/profile"><i class="glyphicon glyphicon-user"></i> Профиль</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="#">Карта</a></li>
                            <li><a href="#">Новости</a></li>
                            <li role="separator" class="divider"></li>
                            <li><a href="/auth/logout"><i class="glyphicon glyphicon-log-out"></i> Покинуть Средиземье</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>



        <!-- Modal -->
        <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Армия</h4>
              </div>
              <div class="modal-body">
                <div class="row">
                    <div class="col-md-2 col-md-offset-1">
                        <p>Отряд 1</p>
                    </div>
                    <div class="col-md-6">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">60%</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span class="label label-warning">Сражается</span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2 col-md-offset-1">
                        <p>Отряд 2</p>
                    </div>
                    <div class="col-md-6">
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">40%</div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <span class="label label-default">Возвращается в замок</span>
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-primary">Сохранить</button>
              </div>
            </div>
          </div>
        </div>

        <? } else { ?>

            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="/auth/login">Царьград</a>
                </div>

                <ul class="nav navbar-nav navbar-right">
                    <li><a href="/auth/register">Регистрация</a></li>
                </ul>
            </div>

        <? } ?>
    </nav>
</header>