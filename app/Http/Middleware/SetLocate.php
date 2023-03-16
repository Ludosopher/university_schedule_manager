<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocate
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
        if (Session()->has('applocale') && in_array(Session()->get('applocale'), config('enum.languages'))) {
            App::setLocale(Session()->get('applocale'));
        }
        else { 
            App::setLocale(config('app.locale'));
        }

        return $next($request);
    }
}
