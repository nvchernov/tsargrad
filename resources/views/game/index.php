<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>

<script type="text/javascript">
    var User = <?= $user->toJson(); ?>;
</script>

<div class="container">
    <div class="row">
        <div class="col-sm-9">
            <img id="gamefield" src="images/gamefield.png" width="700" height="700" usemap="#gamefield-map">
        </div>
        <div class="col-sm-3">
        </div>
    </div>
</div>

<map name="gamefield-map">
    <? $de = 93; $x = $y = 95 ?>
    <? foreach ($castles as $c): ?>
        <? $x1 = $x + $c->location->x * $de; $y1 = $y + $c->location->y * $de; $x2 = $x1 + $de; $y2 = $y1 + $de; ?>
        <area href="javascript:;" user-id="<?= $c->user_id ?>" shape="rect" coords="<?= $x1 ?>, <?= $y1 ?>, <?= $x2 ?>, <?= $y2 ?>">
    <? endforeach; ?>
</map>

<script src="plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script src="js/game.js"></script>

<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
