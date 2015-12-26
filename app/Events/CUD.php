<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 25.12.2015
 * Time: 22:21
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

/**
 * Class CRUD - create, update or remove event.
 * @package App\Events
 */
class CUD extends Event
{
    use SerializesModels;

    /**
     * Пользователь для которого будет отправлено сообщение...
     * @var
     */
    public $user;
    /**
     * Сущность.
     * @var
     */
    public $model;
    /**
     * Тип CUD операции.
     * @var
     */
    public $type;
    /**
     * Конкретные данные.
     * @var array
     */
    public $data;

    public function __construct($user, $type, $model, $data = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->model = $model;
        $this->data = $data;
    }
}