<?php

namespace App\Http\Middleware\role;
use Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestMiddleware
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

        $user = Auth::user();

        if (!$user) {
            return response()->json(['msg' => 'Usuario no encontrado'], 404);
        };

        if ($user->role === 'admin' || $user->role === 'user' ){
            return $next($request);
        }
        
        if ($user->role !== 'guest' ){
            return response()->json(['msg' => 'No tienes los permisos necesarios'], 403);
        }

        return $next($request);
    }
}
