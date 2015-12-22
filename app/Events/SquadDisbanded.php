<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\Squad;

class SquadDisbanded extends Event
{
    use SerializesModels;
    /**
     * @var Squad
     */
    public $squad;
    /**
     * @var array
     */
    public $options;

    /**
     * Create a new event instance.
     *
     * @param Squad $squad
     * @param array $options
     */
    public function __construct(Squad $squad, array $options)
    {
        //
        $this->squad = $squad;
        $this->options = $options;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }

    /**
     * Получить юзера.
     */
    public function user()
    {
        return $this->squad->army->castle->user;
    }

    /**
     * Сообщение для юзера.
     * @return string
     */
    public function message()
    {
        $loots = '';
        foreach ($this->options['loots'] as $key => $val) {
            switch ($key) {
                case 'gold': $loots .= " ЗОЛОТО($val)"; break;
                case 'food': $loots .= " ЕДА($val)"; break;
                case 'wood': $loots .= " ДЕРЕВО($val)"; break;
            }
        }
        $loots = trim($loots);
        return "Отряд '{$this->squad->name}' ({$this->squad->size} чел.) вернулся в замок. Награблено: $loots";
    }
}
