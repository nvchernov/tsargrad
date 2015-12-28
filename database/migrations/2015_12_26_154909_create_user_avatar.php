<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAvatar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mustaches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url')->default('');

            $table->timestamps();
        });

        Schema::create('amulets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url')->default('');

            $table->timestamps();
        });

        Schema::create('hairs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url')->default('');

            $table->timestamps();
        });

        Schema::create('flags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image_url')->default('');

            $table->timestamps();
        });

        Schema::create('avatars', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mustache_id')->unsigned()->nullable();
            $table->integer('amulet_id')->unsigned()->nullable();
            $table->integer('hair_id')->unsigned()->nullable();
            $table->integer('flag_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();

            $table->timestamps();

            $table->foreign('mustache_id')->references('id')->on('mustaches')->onDelete('set null');
            $table->foreign('amulet_id')->references('id')->on('amulets')->onDelete('set null');
            $table->foreign('hair_id')->references('id')->on('hairs')->onDelete('set null');
            $table->foreign('flag_id')->references('id')->on('flags')->onDelete('set null');
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('avatar_id')->unsigned()->nullable();

            $table->foreign('avatar_id')->references('id')->on('avatars')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar_id');
        });
        Schema::table('avatars', function (Blueprint $table) {
            $table->dropForeign('avatars_mustache_id_foreign');
            $table->dropForeign('avatars_amulet_id_foreign');
            $table->dropForeign('avatars_hair_id_foreign');
            $table->dropForeign('avatars_flag_id_foreign');
        });
        Schema::drop('mustaches');
        chema::drop('amulets');
        chema::drop('hairs');
        chema::drop('flags');
    }
}
