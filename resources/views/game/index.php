<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>

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
        <area href="javascript:;" castle-id="<?= $c->id ?>" shape="rect" coords="<?= $x1 ?>, <?= $y1 ?>, <?= $x2 ?>, <?= $y2 ?>">
    <? endforeach; ?>
</map>

<div class="modal fade" id="castleModel" tabindex="-1" role="dialog"></div>

<script src="plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script type="text/javascript">
    var User = $.extend(<?= $user->toJson() ?>, {castle: <?= $user->castle->toJson() ?>});
    var Castles = <?= $castles ? $castles->toJson() : [] ?>;

    // Это текущий пользователь?
    function isSelf(user_id) {
        return user_id == User.id;
    }

    // Это замок текущего пользователя?
    function isSelfCastle(castle_id) {
        return castle_id == User.castle.id;
    }

    var castles = [], areas = [], $gf = $('#gamefield');

    $.each(Castles, function (ind, castle) {
        var id = castle.id;
        if ($.inArray(id) == -1) {
            castles.push(id);

            var area = {key: id, toolTip: castle.name};
            if (isSelf(castle.user_id)) {
                area.render_select = {fillColor: '0000ff'};
                area.toolTip = 'Ваш замок "' + area.toolTip + '"';
            }
            areas.push(area);
        }
    });

    var options = {
        mapKey: 'castle-id',
        staticState: true,
        singleSelect: true,
        render_select: {
            fill: true,
            fillColor: 'ff0000'
        },
        showToolTip: true,
        areas: areas
    };

    options.onClick = function (e) {
        // Свой не показываем...
        if (isSelfCastle(e.key)) { return; }

        $.get('game/castles/' + e.key, function (data) {
            $mod = $('#castleModel');
            $mod.html(data);
            $mod.modal();
        }, 'html');
    };

    $gf.mapster(options);
    $gf.mapster('set', true, castles.join(',')).mapster('set', true, ''+User.id);

</script>

<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
