<?php

namespace App\Http\Middleware\EmailCode;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class ActiveAccount
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
        
        if (!$user->account_active){
            return response()->json(['msg' => 'Usuario no encontrado o deshabilitado'], 423);
        }

        return $next($request);
    }
}
