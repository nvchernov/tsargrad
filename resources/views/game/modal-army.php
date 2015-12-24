<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="armyModalLabel">Моя армия</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal">
                        <fieldset class="col-sm-12">
                            <legend>Состояние армии</legend>
                            <div class="col-sm-12">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="my-army-level badge"><?= $army->level ?></span>
                                        Уровень армии
                                    </li>
                                    <li class="list-group-item">
                                        <span class="my-army badge"><?= $army->size ?></span>
                                        Воинов в замке
                                    </li>
                                    <li class="list-group-item">
                                        <span class="my-army-squad badge"><?= $army->sizeOfSquads ?></span>
                                        Воинов в отрядах
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                        <fieldset class="col-sm-12">
                            <legend>Покупка воинов</legend>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-xs-7">
                                        <p>Цена воина <span class="my-army-price badge"><?= $army->buyPrice() ?></span> <span
                                                class="text-info">Еда</span> и <span
                                                class="my-army-buy-price badge"><?= $army->buyPrice() ?></span> <span
                                                class="text-warning">Дерево</span></p>
                                    </div>
                                    <div class="col-xs-4 col-md-offset-1">
                                        <p>Стоимость <span id="m-army-cost" class="badge">3</span></p>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class="col-xs-3" for="m-army-size"
                                           style="font-weight: 100">Количество</label>

                                    <div class="col-xs-6">
                                        <input id="m-army-size" data-slider-id="sl-army-size"
                                               type="text" data-slider-step="1" data-slider-value="1"
                                               data-slider-min="1"
                                               data-slider-max="1000">
                                    </div>
                                    <div class="col-xs-2">
                                        <button type="button" id="m-army-buy" class="btn btn-primary">Купить</button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="col-sm-12">
                            <legend>Улучшение армии</legend>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-xs-9">
                                        <p>Стоимость перехода на <span
                                                class="my-army-level badge"><?= $army->level + 1 ?></span> уровень <span
                                                class="my-army-buy-upgrade badge"><?= $army->upgradePrice() ?></span>
                                        </p>
                                    </div>
                                    <div class="col-xs-2">
                                        <button type="button" id="m-army-upgrade" class="btn btn-primary">Улучшить
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="col-sm-12">
                            <legend>Отряды</legend>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <? if ($squads->count() > 0): ?>
                                            <? foreach ($squads as $s): ?>
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <span class="badge"><?= $s->size ?></span>
                                                        <?= $s->name ?> <span class="label label-default"><?= $s->hstate ?></span>
                                                    </li>
                                                </ul>
                                            <? endforeach ?>
                                        <? else: ?>
                                            <p>Отсутствуют</p>
                                        <? endif ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
        </div>
    </div>
</div>
<script type="text/javascript">
    var $size = $('#m-army-size'), $cost = $('#m-army-cost');
    $size.slider({tooltip_position: 'bottom'});
    $size.slider().on('change', function (e) {
        console.log(e);
        $cost.text($size.val() * e.value.newValue);
    });

</script>