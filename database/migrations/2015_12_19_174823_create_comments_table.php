<?php
/**
 * @autor: Козлов Дмитрий
 */
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('comments')) { return; }

        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();

            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('parent_comment_id')->nullable()->default(null)->unsigned()->index();
            $table->foreign('parent_comment_id')->references('id')->on('comments')->onDelete('cascade')->onUpdate('cascade');

            $table->integer('comment_block_id')->unsigned()->index();
            $table->foreign('comment_block_id')->references('id')->on('comment_blocks')->onDelete('cascade')->onUpdate('cascade');

            $table->string('hierarchy')->default('');
            $table->string('text');
        });

        Schema::table('comments', function (Blueprint $table) {

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('comments')) { return; }

        Schema::table('comments', function(Blueprint $table) {
            $table->dropForeign('comments_user_id_foreign');
            $table->dropForeign('comments_parent_comment_id_foreign');
            $table->dropForeign('comments_comment_block_id_foreign');
        });

        Schema::drop('comments');
    }
}
