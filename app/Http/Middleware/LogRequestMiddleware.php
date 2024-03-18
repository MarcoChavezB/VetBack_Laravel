<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Logs; 

class LogRequestResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        Logs::create([
            'id_usuario' => auth()->check() ? auth()->user()->id : null, 
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'data_sent' => $request->all(),
            'data_received' => $request->method() == 'GET' ? $request->query() : $response->getContent(),
            'date' => now(),
        ]);

        return $response;
    }
}
