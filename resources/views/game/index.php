<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 22.12.2015
 * Time: 19:04
 */
?>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/header.php'); ?>

<script src="plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script src="plugins/jquery.panzoom/dist/jquery.panzoom.min.js"></script>
<script src="js/game.js"></script>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-9">
            <section class="gamefield">
                <div class="parent">
                    <div class="panzoom-parent" >
                        <img id="gamefield" class="panzoom" src="img/gamefield.png" usemap="#castlesmap">
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
    <area state="s" href="#" shape="rect" coords="49, 49, 95, 95">
</map>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/footer.php'); ?>
