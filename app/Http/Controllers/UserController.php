<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    function getCode($userId){
        $codigo = Str::random(6);
        Cache::put('codigo_' . $userId, $codigo, Carbon::now()->addMinutes(1));
        return $codigo;
    }

    function verifyCode(Request $request) {
        $usuario_id = $request->input('userId');
        $codigo_ingresado = $request->input('codigo');

        $codigo_guardado = Cache::get('codigo_' . $usuario_id);

        if ($codigo_guardado && $codigo_guardado == $codigo_ingresado) {
            Cache::forget('codigo_' . $usuario_id);
            return response()->json(['mensaje' => 'Código válido']);
        }
        return response()->json(['mensaje' => 'Código inválido'], 400);
    }
}
