<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('x')->unsigned();
            $table->integer('y')->unsigned();
            $table->integer('castle_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('castle_id')->references('id')->on('castles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropForeign('locations_castle_id_foreign');
        });
        Schema::drop('locations');
    }
}
