<?php

namespace App\Events;

use App\Models\Squad;
use Illuminate\Queue\SerializesModels;

class SquadAssaulted extends Event
{
    use SerializesModels;

    /**
     * Отряд.
     *
     * @var Squad
     */
    public $squad;

    public $options;

    /**
     * Create a new event instance.
     *
     * @param Squad $squad отряд
     * @param array $options дополнительные опции
     */
    public function __construct(Squad $squad, array $options = [])
    {
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

    private function loots() {
        $loots = '';
        foreach ($this->options['loots'] as $key => $val) {
            switch ($key) {
                case 'gold': $loots .= " ЗОЛОТО($val)"; break;
                case 'food': $loots .= " ЕДА($val)"; break;
                case 'wood': $loots .= " ДЕРЕВО($val)"; break;
            }
        }
        $loots = trim($loots);
        return $loots;
    }

    /**
     * Атакующий.
     */
    public function userAttacker()
    {
        return $this->squad->army->castle->user;
    }

    /**
     * Обороняющийся.
     */
    public function userDefender()
    {
        return $this->squad->goal->user;
    }

    /**
     * Сообщение для атакующих.
     * @return string
     */
    public function messageForAttacker()
    {
        $msg = "Наш отряд '{$this->squad->name}' был полностью разбит у ворот замка '{$this->squad->goal->name}'";
        if ($this->options['status'] == 'win') {
            $loots = $this->loots();
            $msg = "Наш отряд '{$this->squad->name}' победил в битве у замка '{$this->squad->goal->name}'. Награблено: $loots."
                . "Осталось в живых - {$this->squad->size}. Вернется - {$this->squad->crusade_end_at}";
        }
        return $msg;
    }

    /**
     * Сообщение для обороняющихся.
     * @return string
     */
    public function messageForDefender()
    {
        $msg = "Боевая ничья при сражении с отрядом '{$this->squad->name}'. Все войска разбиты";
        if ($this->options['status' == 'win']) {
            $loots = $this->loots();
            $msg = "Вражеский отряд '{$this->squad->name}' полностью разгромил наше войска. Разграблено: $loots. У противника осталось "
                . "в живых - {$this->squad->size}";
        } elseif ($this->options['status' == 'def']) {
            $msg = "Наши войска полностью разгромили вражеский отряд '{$this->squad->name}'. Осталось в живых - {$this->squad->goal->army->size}";
        }
        return $msg;
    }
}
