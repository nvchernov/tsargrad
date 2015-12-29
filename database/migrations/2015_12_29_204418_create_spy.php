<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpy extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spy', function($table){
            $table->increments('id');
            $table->integer('level')->default(1);
            $table->integer('castles_id')->unsigned();
            $table->integer('enemy_castles_id')->unsigned()->nullable();
            $table->foreign('castles_id')->references('id')->on('castles')->onDelete('cascade');
            $table->foreign('enemy_castles_id')->references('id')->on('castles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('spy');
    }
}
