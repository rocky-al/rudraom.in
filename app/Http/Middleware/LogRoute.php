<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Log;

class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        $response = $next($request);

        if (app()->environment('local')) {
            $log = new Log;
            $log->URI = $request->getUri();
            $log->METHOD = $request->getMethod();
            $log->REQUEST_BODY =json_encode($request->all());
            $log->RESPONSE = $response->getContent();
            $log->save();
        }

        return $response;
    }

}
