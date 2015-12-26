<?php

namespace App\Http\Middleware;

use App\Exceptions\GameException;
use App\Models\Squad;
use Log;
use Closure;

class GameArmy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Получить все отряды, которые пора уже рассформировать...
        $toDisband = Squad::readyToDisband()->get();
        foreach ($toDisband as $s) {
            try {
                $s->disband();
            } catch (GameException $exc) {
                // Нужно обрабатывать?
                Log::error($exc->getMessage());
            }
        }

        // Получить все отряды, которым уже должны штурмовать вражеский замок...
        $toAssault = Squad::readyToAssault()->get();
        foreach ($toAssault as $s) {
            try {
                $s->assault();
            } catch (GameException $exc) {
                // Нужно обрабатывать?
                Log::error($exc->getMessage());
            }
        }

        return $next($request);
    }
}
