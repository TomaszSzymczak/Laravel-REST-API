<?php

namespace App\Http\Middleware;

use Closure;

class CheckSomething
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
        if (rand() %2 === 0) {
            exit('Try again later.');
        }
        
        return $next($request);
    }
}
