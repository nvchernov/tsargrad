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
        <div class="col-sm-9">
            <form method="post" action="/post" enctype="multipart/form-data">
                <label>Кому: </label>
                <input name="receiver" id="receiver_name" type="text" class="form-control" placeholder="Имя получателя" value="">

                <label>Текст письма:</label>
                <textarea name="text" id="text" class="form-control" rows="12" placeholder="Текст"></textarea><br/>
                <input type="submit" value="Отправить" class="btn btn-primary" >
            </form>
        </div>
    </body>

</html>