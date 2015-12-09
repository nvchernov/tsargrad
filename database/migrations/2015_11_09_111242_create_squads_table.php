<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSquadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('squads')) { return; }

        Schema::create('squads', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->index();
            $table->integer('size')->unsigned();
            $table->dateTime('crusade_at')->nullable();
            $table->dateTime('battle_at')->nullable();
            $table->dateTime('crusade_end_at')->nullable();
            $table->integer('army_id')->unsigned()->index();
            $table->integer('goal_id')->unsigned()->index();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('squads', function (Blueprint $table) {
            $table->foreign('army_id')->references('id')->on('armies')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('goal_id')->references('id')->on('castles')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('squads')) { return; }

        Schema::table('squads', function(Blueprint $table) {
            $table->dropForeign('squads_army_id_foreign');
            $table->dropForeign('squads_goal_id_foreign');
        });

        Schema::drop('squads');
    }
}
