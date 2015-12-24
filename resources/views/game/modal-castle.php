<div class="modal-dialog">
    <div class="modal-content" role="document">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="castleModalLabel">Вражеский замок <strong><?= $castle->name ?></strong></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <form class="form-horizontal">
                        <fieldset class="col-sm-12">
                            <legend>Приблизительные ресурсы замка</legend>
                            <div class="col-sm-12">
                                <ul class="list-group">
                                    <li class="list-group-item list-group-item-warning">
                                        <span class="badge"><?= $resources->get('gold') ?: 0 ?></span>
                                        Золото
                                    </li>
                                    <li class="list-group-item list-group-item-success">
                                        <span class="badge"><?= $resources->get('wood') ?: 0 ?></span>
                                        Дерево
                                    </li>
                                    <li class="list-group-item list-group-item-info">
                                        <span class="badge badge-"><?= $resources->get('food') ?: 0 ?></span>
                                        Еда
                                    </li>
                                </ul>
                            </div>
                        </fieldset>
                        <? if ($user->army->size > 0): ?>
                            <fieldset class="col-sm-12">
                                <legend>Подготовить нападение</legend>
                                <div class="col-sm-12">
                                    <div class='form-group'>
                                        <label class="col-xs-4 control-label" for="m-squad-name">Имя отряда</label>

                                        <div class="col-xs-8">
                                            <input class="form-control" id="m-squad-name" type="text">
                                            <span id="m-squad-name-h"
                                                  class="help-block hidden">Не указано имя отряда</span>
                                        </div>
                                    </div>
                                    <div class='form-group'>
                                        <label class="col-xs-4 control-label" for="m-squad-size">Размер отряда</label>

                                        <div class="col-xs-8">
                                            <input class="form-control" id="m-squad-size" data-slider-id="sl-squad-size"
                                                   type="text" data-slider-step="1"
                                                   data-slider-value="1" data-slider-min="1"
                                                   data-slider-max="<?= $user->army->size ?>">
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
                <? if ($user->army->size > 0): ?>
                    <button id="m-crusade" type="button" class="btn btn-danger">Напасть</button>
                <? endif; ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $size = $('#m-squad-size');
    $size.slider({tooltip_position: 'bottom'});

    $('#m-crusade').click(function () {
        var $name = $("#m-squad-name"), $nameHelp = $('#m-squad-name-h');
        if ($name.val() == '') {
            $nameHelp.removeClass('hidden');
            $name.closest('.form-group').addClass('has-error');
            return;
        }
        $nameHelp.addClass('hidden');
        $name.closest('.form-group').removeClass('has-error');

        $.post('game/armies/<?= $user->army->id ?>/crusade',
            {name: $name.val(), count: $size.val(), goal: <?= $castle->id ?>},
            function (resp) {
                var options = {theme: 'bootstrapTheme', closeWith: ['button']};
                if (resp.success) {
                    var Squad = resp.data;
                    options.text = 'Отряд "' + Squad.name + '" (' + Squad.size + ' чел.) отправился в поход на вражеский замок ' +
                        '"' + Squad.goal.name + '" (от ' + Squad.crusade_at + '). Сражение состоится ' + Squad.battle_at;
                    options.type = 'success';
                } else {
                    options.text = resp.message;
                    options.type = 'error';
                }
                noty(options);
            }, 'json'
        );

        $('#castle-modal').modal('hide');
    });
</script>