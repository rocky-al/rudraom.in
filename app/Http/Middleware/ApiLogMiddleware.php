<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;

class ApiLogMiddleware
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
        return $next($request);
    }

    public function terminate($request, $response) {
        $data = new ApiLog();
        $data->url = $request->fullUrl();
        $data->method = $request->method();
        $data->header = serialize($request->header());
        $data->ip = $request->ip();
        $data->params = serialize($request->getContent());
        $data->response = serialize($response);
        $data->save();
     }
}

