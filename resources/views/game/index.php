<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_header.php'); ?>
<?php App::setLocale('ru'); ?>

<link rel="stylesheet" href="/plugins/bootstrap-slider/bootstrap-slider.min.css">

<div class="container"  style="display: <?php if (is_null($attack) || $attack->status != 0): ?> block;"  <?php else: ?> none;" <?php endif; ?>>
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
                    <img id="gamefield" width="600" height="600" src="/images/gamefield.png" usemap="#gamefield-map">
                </div>
            </div>
            <div class="col-sm-3" id="right_game_panel">
                <h2> Мой замок <strong><?= $user->castle->name ?></strong></h2>
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Ресурсы</h3>
                        <div id="my-resources"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div id="my_spy">
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#my-spy-modal">
                            Мои шпионы
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h3>Строения</h3>
                        <div id="my-buildings">
                            <table class="table table-bordered table-hover">
                            <?php foreach($buildings as $build): ?>
                                <tr>
                                    <td data-level="<?=$build->level;?>" id="<?=$build->buildingType()->first()->building_name;?>" class="text-center"><b><?=trans('game.'.$build->buildingType()->first()->building_name); ?> (<?=$build->level; ?> ур.)</b>
                                        <div class="small-top-line">
                                            Цена <span class="badge"><?=$build->level+1; ?></span> уровня<br/>
                                            <span class="badge"><?=$build->costUpdate();?></span> 
                                            <span class="text-warning">Золота</span><br/>
                                            <span class="badge"><?=$build->costUpdate();?></span> 
                                            <span class="text-success">Дерева</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><button class="btn btn-danger update_build_button" data-id="<?=$build->id;?>">up</button></td>
                                </tr>
                            <?php endforeach; ?>  
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<map name="gamefield-map">
    <?php $de = 93;
    $x = $y = 95 ?>
    <?php foreach ($castles as $c): ?>
        <?php $x1 = $x + $c->location->x * $de;
        $y1 = $y + $c->location->y * $de;
        $x2 = $x1 + $de;
        $y2 = $y1 + $de; ?>
        <area href="javascript:;" castle-id="<?= $c->id ?>" shape="rect"
              coords="<?= $x1 ?>, <?= $y1 ?>, <?= $x2 ?>, <?= $y2 ?>">
    <?php endforeach; ?>
</map>

<div class="modal fade" id="enemy-castle-modal" tabindex="-1" role="dialog"></div>
<div class="modal fade" id="my-spy-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Мои шпионы</h3>
            </div>
            <div class="modal-body">
                <div class="container-fluid text-center" style="margin-bottom: 20px;">
                    <button type="button" class="btn btn-danger" id="buy_new_spy">Нанять нового шпиона (200 ед. золота)</button>
                </div>
                <div class="container-fluid">
                    <?php if(!empty($spies)) : ?>
                        <table class="table table-bordered table-hover">
                        <?php foreach($spies as $spy): ?>
                            <tr>
                                <td>Шпион #<?=$spy->id;?></td>
                            </tr>
                        <?php endforeach; ?> 
                        </table>
                    <?php endif; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>    
</div>
<?php if (is_null($attack) || $attack->status != 0): ?>
<?php else: ?>
    <?php echo view('pve_enemy_attack/battle',['attack' => $attack]); ?>
<?php endif; ?>
<?php if (is_null($attack) || $attack->status != 0): ?>
<script src="/plugins/image-mapster/jquery.imagemapster.min.js"></script>
<script src="/plugins/jquery.ui/jquery-ui.min.js"></script>
<script type="text/javascript">
    (function () {
        
        // Upgrade building
        $(document).on('click', '.update_build_button', function() {
            var idInitial = $(this).attr('data-id');
            $.post('/game/building/' + idInitial + '/upgrade', function(data) {  
                if(data == "no_costs") {
                    alert('Не хватает ресурсов');
                } else if (data == "success") {
                    $('#my-buildings').load('/game #my-buildings > table');                    
                }                
            });
        });
        
        $(document).on('click', '#buy_new_spy', function() {
            $.post('/game/spy/new', function(data) { 
                if(data == "no_costs") {
                    alert('Не хватает ресурсов');
                } else if (data == "success") {
                    $('#my-spy-modal').load('/game #my-spy-modal .modal-dialog');                    
                } 
            });
        });
       
        function recalcResources() {
            var arr  = [ ['wood', 'sawmill'], ['gold', 'mine'], ['food', 'farm']];
            arr.forEach( function(item, i, arr) {
                var prevValue = player.resources.get(item[0]).get('count');
                player.resources.get(item[0]).set('count', prevValue + +$('#' + item[1]).attr('data-level'));                
            });           
        }; setInterval(recalcResources, 1000);
        
        function Init() {
            
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

                $.get('/game/castles/' + e.key, function (resp) {
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

            // Грязный грязный хак...
            setTimeout(function() {
                var $gfWrapper = $('#mapster_wrap_0');
                var $gfMask = $("#gf-mask");

                // Сделать ресайз области карты...
                var doResize = function () {
                    $gfWrapper.css({top: 0, left: 0});

                    // и это тоже хак...
                    var maskWidth = $gfMask.width() || 598;
                    var maskHeight = $gfMask.height() || 598;
                    var imgPos = $gfWrapper.offset() || {left: 187.25, top: 169};
                    var imgWidth = $gfWrapper.width() || 600;
                    var imgHeight = $gfWrapper.height() || 600;

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
            }, 100);

        } Init();
        
    }());
</script>
<?php endif; ?>
<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/../resources/views/layout/master_footer.php'); ?>
