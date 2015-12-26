<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 25.12.2015
 * Time: 22:00
 */

namespace App\Listeners;

use LRedis;

class GameListener
{
    public function onSquadAssault($e)
    {
        $redis = LRedis::connection();
        $redis->publish('message', json_encode([
            'event' => "user/{$e->userAttacker()->id}", 'data' => $e->messageForAttacker()
        ]));

        $redis->publish('message', json_encode([
            'event' => "user/{$e->userDefender()->id}", 'data' => $e->messageForDefender()
        ]));
    }

    public function onSquadDisband($e)
    {
        $redis = LRedis::connection();
        $redis->publish('message', json_encode([
            'event' => "user/{$e->user()->id}", 'data' => $e->message()
        ]));
    }

    public function onCUD($e)
    {
        $entity = strtolower ((new \ReflectionClass($e->model))->getShortName());
        $event = "user/{$e->user->id}/$entity/{$e->type}";
        $data = empty($e->data) ? $e->model->jsonSerialize() : $e->data;

        $redis = LRedis::connection();
        $redis->publish('message', json_encode(['event' => $event, 'data' => $data]));
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\CUD',
            'App\Listeners\GameListener@onCUD'
        );
        $events->listen(
            'App\Events\SquadAssaulted',
            'App\Listeners\GameListener@onSquadAssault'
        );
        $events->listen(
            'App\Events\SquadDisbanded',
            'App\Listeners\GameListener@onSquadDisband'
        );
    }
}