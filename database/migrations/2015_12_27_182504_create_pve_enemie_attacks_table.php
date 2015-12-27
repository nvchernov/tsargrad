<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePveEnemieAttacksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pve_enemy_attacks', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pve_enemy_id')->unsigned()->index();
            $table->foreign('pve_enemy_id')->references('id')->on('pve_enemies')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('castle_id')->unsigned()->index();
            $table->foreign('castle_id')->references('id')->on('castles')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('demanded_resource_id')->unsigned()->index();
            $table->foreign('demanded_resource_id')->references('id')->on('resources')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('demanded_resource_count')->unsigned();

            $table->boolean('is_user_win');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pve_enemy_attacks');
    }
}
