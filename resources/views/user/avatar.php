<?
/**
 * Created by PhpStorm.
 * User: Козлов Дмитрий
 * Date: 26.12.2015
 * Time: 19:57
 */
?>
<!--<img src="http://bootdey.com/img/Content/user_1.jpg" class="avatar" alt="--><?//=$user->name?><!--">-->

<div class="avatar" style="width: 60px; position: relative; float: left;">
    <div class="row" style="position: relative; z-index: 2;">
        <img id="avatar_top" style="max-width: 100%; margin-left: 15px;  height: auto;" src="<?php echo App\Models\Hair::find(App\Models\Avatar::find($user->avatar_id)->hair_id)->image_url ?>" >
        <input id="hair_id" name="hair_id" placeholder="Имя полководца" type="hidden" value="<?php //echo $avatar->hair_id ?>">
    </div>

    <div class="row" style="position: relative; z-index: 2;">
        <img id="avatar_middle" style="margin-left: 15px;  max-width: 100%;height: auto;" src="<?php echo App\Models\Mustache::find(App\Models\Avatar::find($user->avatar_id)->mustache_id)->image_url ?>" >
        <input id="mustache_id" name="mustache_id" placeholder="Имя полководца" type="hidden" value="<?php //echo $avatar->mustache_id ?>">
    </div>

    <div class="row" style="position: relative; z-index: 2;">
        <img id="avatar_bottom" style="margin-left: 15px; max-width: 100%; height: auto;" src="<?php echo App\Models\Amulet::find(App\Models\Avatar::find($user->avatar_id)->amulet_id)->image_url ?>" >
        <input id="amulet_id" name="amulet_id" placeholder="Имя полководца" type="hidden" value="<?php //echo $avatar->amulet_id ?>">
    </div>

    <div style=" position: absolute; top: 0; z-index: 1;">
        <img style="width: 88px;" id="avatar_flag" src="<?php echo App\Models\Flag::find(App\Models\Avatar::find($user->avatar_id)->flag_id)->image_url ?>" >
    </div>
</div>