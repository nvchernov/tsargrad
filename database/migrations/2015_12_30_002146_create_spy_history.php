<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpyHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spy_history', function($table){
            $table->increments('id');
            $table->integer('spy_id')->unsigned();
            $table->integer('squads_id')->unsigned();
            $table->boolean('detect');
            $table->foreign('spy_id')->references('id')->on('spy');
            $table->foreign('squads_id')->references('id')->on('squads');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('spy_history');
    }
}
