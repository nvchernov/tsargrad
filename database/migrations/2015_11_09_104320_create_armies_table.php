<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArmiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('armies', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->index();
            $table->integer('size')->unsigned();
            $table->integer('level')->unsigned();
            $table->integer('castle_id')->unsigned();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('armies', function (Blueprint $table) {
            $table->foreign('castle_id')->references('id')->on('castles')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('armies', function(Blueprint $table) {
            $table->dropForeign('armies_castle_id_foreign');
        });

        Schema::drop('armies');
    }
}
