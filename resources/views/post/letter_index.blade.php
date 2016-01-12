<?php
use App\Models\User;
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

    <div class="col-xs-2">
        <form method="get" action="/post/create" enctype="multipart/form-data">
            <input type="submit" value="Написать письмо" class="btn btn-primary" >
        </form>
    </div>

    <fieldset class="col-sm-12">
        <legend>Входящие письма</legend>

        @foreach($letters as $letter)

        <div class="col-sm-12">
            <div class="form-group">
                <form method="post" action="/post/{{$letter->id}}" enctype="multipart/form-data">
                    <div class="col-xs-9">
                        <p>От кого: {{ User::find($letter->sender_id)->name }}</p>
                        <p>Текст: {{ $letter->text }}</p>
                        <p>Дата: {{ $letter->created_at }}</p>
                    </div>
                    <div class="col-xs-2">
                        <input type="hidden" name="_method" value="delete">
                        <input type="submit" value="Удалить" class="btn btn-primary" >
                    </div>
                </form>
            </div>
        </div>

        @endforeach
    </fieldset>

    </body>
</html>