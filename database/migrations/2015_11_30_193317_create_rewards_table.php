<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('rewards')) { return; }

        Schema::create('rewards', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('count')->unsigned();
            $table->integer('squad_id')->unsigned()->index();
            $table->integer('resource_id')->unsigned()->index();

            $table->timestamps();
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->unique(['squad_id', 'resource_id']);

            $table->foreign('squad_id')->references('id')->on('squads')->onDelete('cascade');
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('rewards')) { return; }

        Schema::table('rewards', function(Blueprint $table) {
            $table->dropForeign('rewards_squad_id_foreign');
            $table->dropForeign('rewards_resource_id_foreign');
        });

        Schema::drop('rewards');
    }
}
