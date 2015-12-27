<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>

<link rel="stylesheet" href="/plugins/bootstrap-slider/bootstrap-slider.min.css">

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-sm-9">
                <img id="gamefield" src="images/gamefield.png" width="700" height="700" usemap="#gamefield-map">
            </div>
            <div class="col-sm-3">
                <h2> Мой замок <strong><?= $user->castle->name ?></strong></h2>
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Ресурсы</h3>
                        <div id="my-resources"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Строения</h3
                    </div>
                </div>
            </div>
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

<div class="modal fade" id="enemy-castle-modal" tabindex="-1" role="dialog"></div>

<script src="/plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script type="text/javascript">
    // Базовые сущности...
    enemy.castle = new Models.Castle();
    enemy.resources = new Models.Resources(defaultResources);

    $('#enemy-castle-modal').html((new Views.EnemyCastle({model: player.army, castle: enemy.castle, resources: enemy.resources})).render().el);

    $('#my-resources').append((new Views.Resources({collection: player.resources})).render().el);

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

            var area = {key: ''+id};
            if (isSelf(castle.user_id)) {
                area.render_select = {fillColor: '0000ff'};
                area.toolTip = 'Мой замок';
            } else {
                area.toolTip = 'Вражеский замок ' + castle.name;
            }
            areas.push(area);
        }
    });

    var options = {
        mapKey: 'castle-id',
        staticState: true,
        singleSelect: true,
        render_select: {
            fillColor: 'ff0000'
        },
        showToolTip: true,
        areas: areas
    };
    options.onClick = function (e) {
        // Свой не показываем...
        if (isSelfCastle(e.key)) { return; }

        $.get('game/castles/' + e.key, function (resp) {
            if (resp.success) {
                player.army.set(resp.data.army);
                enemy.castle.set(resp.data.enemy_castle);
                enemy.resources.set(_.defaults(resp.data.enemy_resources, defaultResources));
                $('#enemy-castle-modal').modal();
            }
        }, 'json');
    };

    $gf.mapster(options);
    $gf.mapster('set', true, castles.join(','));

</script>

<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
