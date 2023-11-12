<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use URL;
use Carbon\Carbon;


class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale($request->segment(1)); // <-- Set the application locale
        Carbon::setLocale($request->segment(1)); // <-- Set the Carbon locale
     
        URL::defaults(['locale' => $request->segment(1)]); // <-- Set the URL defaults
        // (for named routes we won't have to specify the locale each time!)
     
        return $next($request);
    }
}
