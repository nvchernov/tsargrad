<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMegasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('megas', function($table){
            $table->increments('id');
            $table->string('string');
            $table->integer('integer');
            $table->string('image');
            $table->text('text');
            $table->boolean('boolean');
            $table->time('time');
            $table->date('date');
            $table->enum('choices', ['foo', 'bar']);
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
        Schema::drop('megas');
    }
}
