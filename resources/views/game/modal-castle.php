<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="castleModalLabel">Вражеский замок <strong><?= $castle->name ?></strong></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-12">
                    <form id="enemy-castle" class="form-horizontal">
                        <fieldset class="col-sm-12">
                            <legend>Приблизительные ресурсы замка</legend>
                            <div id="enemy-resources" class="col-sm-12"></div>
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
    var enemyResources = new Models.Resources(_.defaults(<?= $resources->toJson() ?>, defaultResources));
    $('#enemy-resources').html((new Views.Resources({collection: enemyResources})).render().el);

    var armyCrusade = new Views.ArmyCrusade({model: player.army, goalid: <?= $castle->id ?>});
    $('#enemy-castle').append(armyCrusade.render().el);
</script>