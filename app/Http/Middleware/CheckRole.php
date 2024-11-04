<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
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
        if($request->user() === null)
        {
            return response('Accès Reffusé', 401);
        }

        if($request->user()->roles === 'XJZT' || $request->user()->roles === 'ADMIN' || $request->user()->roles === 'ROOT')
        {
            return $next($request);
        }
        return response('Accès Reffusé', 401);
    }
}
