<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>

<link rel="stylesheet" href="/plugins/bootstrap-slider/bootstrap-slider.min.css">

<div class="container">
    <div class="content">
        <div class="row">
            <div class="col-sm-9" style="text-align: center">
                <h3>Средиземье</h3>
                <div class="gf-resizer">
                    <form>
                        <div class="form-group">
                            <label for="gf-resizer" style="font-weight: 100; margin-right: 10px">Масштаб</label>

                            <input id="gf-resizer" data-slider-id="sl-gf-resizer" type="text" data-slider-step="0.1"
                                   data-slider-value="0.6" data-slider-min="0.6" data-slider-max="1.2">
                        </div>
                    </form>

                </div>
                <div id="gf-mask" style=" width: 600px; height: 600px; overflow: hidden;">
                    <img id="gamefield" width="600" height="600" src="images/gamefield.png" usemap="#gamefield-map">
                </div>
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
    <? $de = 93;
    $x = $y = 95 ?>
    <? foreach ($castles as $c): ?>
        <? $x1 = $x + $c->location->x * $de;
        $y1 = $y + $c->location->y * $de;
        $x2 = $x1 + $de;
        $y2 = $y1 + $de; ?>
        <area href="javascript:;" castle-id="<?= $c->id ?>" shape="rect"
              coords="<?= $x1 ?>, <?= $y1 ?>, <?= $x2 ?>, <?= $y2 ?>">
    <? endforeach; ?>
</map>

<div class="modal fade" id="enemy-castle-modal" tabindex="-1" role="dialog"></div>

<script src="/plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script src="/plugins/jquery.ui/jquery-ui.min.js"></script>
<script type="text/javascript">
    (function () {
        var User = $.extend(<?= $user->toJson() ?>, {castle: <?= $user->castle->toJson() ?>});
        var Castles = <?= $castles ? $castles->toJson() : [] ?>;

        // Базовые сущности...
        enemy.castle = new Models.Castle();
        enemy.resources = new Models.Resources(defaultResources);

        // Рендеринг.
        $('#enemy-castle-modal').html((new Views.EnemyCastle({
            model: player.army,
            castle: enemy.castle,
            resources: enemy.resources
        })).render().el);
        $('#my-resources').append((new Views.Resources({collection: player.resources})).render().el);

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

                var area = {key: '' + id};
                if (isSelf(castle.user_id)) {
                    area.render_select = {fillColor: 'ff0000'};
                    area.toolTip = 'Мой замок';
                } else {
                    area.toolTip = castle.name;
                }
                areas.push(area);
            }
        });

        var options = {
            mapKey: 'castle-id',
            staticState: true,
            singleSelect: true,
            render_select: {
                fillColor: '0000ff'
            },
            showToolTip: true,
            areas: areas
        };
        options.onClick = function (e) {
            // Свой не показываем...
            if (isSelfCastle(e.key)) {
                return;
            }

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

        var $gfWrapper = $('#mapster_wrap_0');
        var $gfMask = $("#gf-mask");

        // Сделать ресайз области карты...
        var doResize = function () {
            $gfWrapper.css({top: 0, left: 0});

            var maskWidth = $gfMask.width();
            var maskHeight = $gfMask.height();
            var imgPos = $gfWrapper.offset();
            var imgWidth = $gfWrapper.width();
            var imgHeight = $gfWrapper.height();

            var x1 = (imgPos.left + maskWidth) - imgWidth;
            var y1 = (imgPos.top + maskHeight) - imgHeight;
            var x2 = imgPos.left;
            var y2 = imgPos.top;

            $gfWrapper.draggable({containment: [x1, y1, x2, y2]});
            $gfWrapper.css({cursor: 'move'});
        };

        $('#gf-resizer').slider().on('change', function (e) {
            $gfWrapper.draggable('destroy');
            $gf.mapster('resize', 1000 * e.value.newValue, 1000 * e.value.newValue, 0);
            doResize();
        });
        // первоначальный drag and drop.
        doResize();
    }());
</script>

<? require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
