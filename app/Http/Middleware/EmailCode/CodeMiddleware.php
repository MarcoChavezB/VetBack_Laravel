<?php

namespace App\Http\Middleware\EmailCode;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class CodeMiddleware
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
        
        if (!$user->code_verified){
            return response()->json(['msg' => 'Aun no verificas tu cuenta 2FA'], 403);
        }

        return $next($request);
    }
}
