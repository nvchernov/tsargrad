<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('scores')) { return; }

        Schema::create('scores', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('count')->unsigned();
            $table->integer('castle_id')->unsigned();
            $table->integer('resource_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('scores', function (Blueprint $table) {
            $table->unique(['castle_id', 'resource_id']);

            $table->foreign('castle_id')->references('id')->on('castles')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('scores')) { return; }

        Schema::table('scores', function(Blueprint $table) {
            $table->dropForeign('scores_castle_id_foreign');
            $table->dropForeign('scores_resource_id_foreign');
        });

        Schema::drop('scores');
    }
}
