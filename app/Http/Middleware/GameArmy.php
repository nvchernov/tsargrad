<?php

namespace App\Http\Middleware;

use App\Exceptions\GameException;
use App\Models\Squad;
use Carbon\Carbon;
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
        $now = Carbon::now();

        // Получить все отряды, которые пора уже рассформировать...
        $forDisband = Squad::whereNotNull('crusade_end_at')->where('crusade_end_at', '<=', $now)->get();
        foreach ($forDisband as $s) {
            try {
                $s->disband();
            } catch (GameException $exc) {
                // Нужно обрабатывать?
                Log::error($exc->getMessage());
            }
        }

        // Получить все отряды, которым уже должны штурмовать вражеский замок...
        $forAssault = Squad::whereNull('crusade_end_at')->where('battle_at', '<=', $now)->get();
        foreach ($forAssault as $s) {
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
