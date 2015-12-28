<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function($table){
            $table->increments('id');
            $table->string('building_name');
        });
        
        Schema::create('buildings_castles', function($table2){
            $table2->increments('id');
            $table2->integer('castles_id')->unsigned();
            $table2->integer('buildings_id')->unsigned();
            $table2->integer('level')->default(1);
            $table2->foreign('castles_id')->references('id')->on('castles')->onDelete('cascade');
            $table2->foreign('buildings_id')->references('id')->on('buildings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('buildings');
    }
}
