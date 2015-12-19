<?php
/**
 * @autor: Козлов Дмитрий
 */
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsOnInsertTrigger extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
              CREATE TRIGGER CreateCommentsAfterInsertTrigger AFTER INSERT ON `comments` FOR EACH ROW
                    BEGIN
                        DECLARE hierarchy VARCHAR(255);
                        SELECT CONCAT(`hierarchy`,`/`,NEW.id) into hierarchy FROM `comments` WHERE `comments`.`id` = NEW.parent_comment_id;
                        UPDATE `comments` SET `hierarchy` = hierarchy WHERE `id` = NEW.id;
                    END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `CreateCommentsAfterInsertTrigger`');
    }
}
