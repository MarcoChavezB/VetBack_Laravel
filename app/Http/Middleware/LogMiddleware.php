<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use Closure;
use Illuminate\Http\Request;
use App\Models\Logs; 

class LogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $queries = [];

        if ($request->isMethod('GET')) {
            DB::listen(function ($query) use (&$queries) {
                $queries[] = [
                    'query' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time,
                ];
            });
        }

        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        Logs::create([
            'id_usuario' => auth()->check() ? auth()->user()->id : null, 
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'data_sent' => $request->all(),
            'data_received' => $request->method() == 'GET' ? json_encode($queries) : $response->getContent(),
            'date' => now()->toDateTimeString(),
        ]);        

        return $response;
    }
}
