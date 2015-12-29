<?php
/**
 * Created by PhpStorm.
 * User: Козлов Дмитрий
 * Date: 29.12.2015
 * Time: 18:03
 */
    $enemy = $attack->enemy();
?>
<div class="row">
    <div class="col-lg-10 col-lg-offset-1">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>На нас напали! <?=$enemy->name?> приближается к нам со своей армией!</h4>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <img src="images/orc.jpg" class="img-rounded" style="width:100%"/>
                    </div>
                    <div class="col-lg-10">
                        <pre class="lead"><?=$enemy->name?>: <i>«<?=$enemy->message?>»</i></pre>
                        <pre class="lead">Требования: <?=$attack->demanded_resource_count?> <?
                            switch( $attack->resource()->name ) {
                                case 'gold': echo 'золота'; break;
                                case 'wood': echo 'дерева'; break;
                                case 'food': echo 'еды'; break;
                            }
                        ?></pre>
                        <pre class="lead">Численность армии: <?=$attack->army_count?></pre>
                        <pre class="lead">Уровень армии: <?=$attack->army_level?></pre>
                    </div>
                </div>
            </div>
            <div class="panel-footer clearfix" >
                <div class="btn-group btn-group-lg pull-right" role="group" aria-label="...">
                    <a class="btn btn-danger" href="surrender">Сдаться</a>
                    <a class="btn btn-default" href="joinBattle">Вступить в бой!</a>
                </div>
            </div>
        </div>
    </div>
</div>
