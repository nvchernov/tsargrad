<div class="modal-dialog">
    <div class="modal-content" role="document">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="castleModalLabel">Вражеский замок "<?= $castle->name ?>"</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal">
                        <fieldset class="col-sm-12">
                            <legend>Приблизительные ресурсы замка</legend>
                            <div class="col-sm-12">
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <span class="badge"><?= $castle->getResources('gold') ?: 0 ?></span>
                                        Золото
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge"><?= $castle->getResources('wood') ?: 0 ?></span>
                                        Дерево
                                    </li>
                                    <li class="list-group-item">
                                        <span class="badge badge-"><?= $castle->getResources('food') ?: 0 ?></span>
                                        Еда
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                        <? if ($user->castle->army->size > 0): ?>
                            <fieldset class="col-sm-12">
                                <legend>Подготовить нападение</legend>
                                <div class="col-sm-12">
                                    <div class='form-group'>
                                        <label class="col-xs-4 control-label" for="m-squad-name">Имя отряда</label>

                                        <div class="col-xs-8">
                                            <input class="form-control" id="m-squad-name" type="text">
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class="col-xs-4 control-label" for="m-squad-size">Размер отряда</label>

                                        <div class="col-xs-8">
                                            <input class="form-control" id="m-squad-size" data-slider-id="sl-squad-size"
                                                   type="text" data-slider-step="1"
                                                   data-slider-value="14" data-slider-min="1"
                                                   data-slider-max="<?= $user->castle->army->size ?>">
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        <? endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <form class="form-horizontal">
                <? if ($user->castle->army->size > 0): ?>
                    <button id="m-crusade" type="button" class="btn btn-danger">Напасть</button>
                <? endif; ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </form>
        </div>
    </div>
</div>
<script
<script type="text/javascript">
    $('#m-squad-size').slider({tooltip_position: 'bottom', tooltip: 'always'});
    $('#m-crusade').on('click', function () {

    });
</script>