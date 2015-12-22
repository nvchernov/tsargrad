<?php
/**
 * Created by PhpStorm.
 * User: Роман
 * Date: 22.12.2015
 * Time: 3:18
 */

namespace App\Http\Middleware;

use Closure;

class VerifyJson
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
        if ($request->isJson() && $request->wantsJson()) {
            return $next($request);
        }

        abort(404);
    }
}
