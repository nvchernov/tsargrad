<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/header.php'); ?>

<script src="plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script src="plugins/jquery.panzoom/dist/jquery.panzoom.min.js"></script>
<script src="js/game.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-9">
            <section class="gamefield">
                <div class="parent">
                    <div class="panzoom-parent">
                        <img id="gamefield" class="panzoom" src="images/gamefield.png" usemap="#castlesmap">
                    </div>
                </div>
                <form class="form-inline">
                    <button class="zoom-in">+</button>
                    <button class="zoom-out">-</button>
                    <input type="range" class="zoom-range">
                    <button class="reset">Сбросить</button>
                </form>
            </section>
        </div>
    </div>
</div>

<map name="castlesmap">
    <?php
    $de = 93;
    $x = $y = 95

    ?>
    <?php foreach ($castles as $c): ?>
        <?php $x1 = $x + $c->location->x * $de; $y1 = $y + $c->location->y * $de; $x2 = $x1 + $de; $y2 = $y1 + $de; ?>
        <area castle-id="<?php $c->id ?>" shape="rect" coords="<?php $x1 ?>, <?php $y1 ?>, <?php $x2 ?>, <?php $y2 ?>">
        <?php //echo("<area state='$x2-$y2' shape='rect' coords='$x1, $y1, $x2, $y2' href='#'>"); ?>
    <?php endforeach; ?>
</map>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/footer.php'); ?>
