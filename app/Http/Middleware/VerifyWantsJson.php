<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 22.12.2015
 * Time: 3:18
 */

namespace App\Http\Middleware;

use Closure;

class VerifyWantsJson
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
        if ($request->wantsJson()) {
            return $next($request);
        }

        abort(404);
    }
}
