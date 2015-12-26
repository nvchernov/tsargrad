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
        /*DB::unprepared('
              CREATE TRIGGER CreateCommentsAfterInsertTrigger BEFORE INSERT ON `comments` FOR EACH ROW
                    BEGIN
                        DECLARE h VARCHAR(255);
                        SET h = (SELECT `hierarchy` FROM `comments` WHERE `comments`.`id` = NEW.parent_comment_id);
                        IF h IS NULL THEN
                            SET h = "";
                        END IF;
                        SET NEW.hierarchy = CONCAT(h,"-",NEW.id);
                    END
        ');*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*DB::unprepared('DROP TRIGGER `CreateCommentsAfterInsertTrigger`');*/
    }
}
