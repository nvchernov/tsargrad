<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'post';

    protected $dates = ['deleted_at', 'created_at'];

    /**
     * @param $sender_id Отправитель
     * @param $receiver_id Получатель
     * @param $text Текст
     */
    public static function createMessage($sender_id, $receiver_id, $text)
    {
        $message = new this;
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiver_id;
        $message->text = $text;
        $message->save();
    }
}
