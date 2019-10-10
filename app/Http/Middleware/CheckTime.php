<?php

namespace App\Http\Middleware;

use Closure;

class CheckTime
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
        if (now() < "2019-09-19 00:01") {
            exit('Ta część serwisu będzie dostępna dopiero po 19-stym.');
        }
        
        $response = $next($request);
        return $response;
    }
}
