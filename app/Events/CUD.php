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
 * Class CRUD - create, update or remove
 * @package App\Events
 */
class CUD extends Event
{
    use SerializesModels;

    public $user;
    public $model;
    public $type;
    public $data;

    public function __construct($user, $type, $model, $data = [])
    {
        $this->user = $user;
        $this->type = $type;
        $this->model = $model;
        $this->data = $data;
    }
}