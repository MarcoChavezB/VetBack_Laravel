<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Logs;
class LogController extends Controller
{

public function getLogs() {
    $logs = Logs::all();

    if ($logs->isEmpty()) {
        return response()->json(['data' => [], 'success' => false], 400);
    } else {
        $userIds = $logs->pluck('id_usuario')->unique();

        $usuarios = User::whereIn('id', $userIds)->take(10)->get()->keyBy('id');

        $logsConUsuarios = $logs->map(function ($log) use ($usuarios) {
            $usuario = $usuarios->get($log->id_usuario);
        
            if ($usuario) {
                $log->name = $usuario->name;
                $log->email = $usuario->email;
            } else {
                $log->name = null;
                $log->email = null;
            }
        
            return $log;
        });
        return response()->json(['data' => $logsConUsuarios, 'success' => true], 200);
    }
}

    

public function logsMethodGet() {
    $logs = Logs::where('method', 'GET')->get();

    if ($logs->isEmpty()) {
        return response()->json(['data' => [], 'success' => false], 400);
    } else {
        $userIds = $logs->pluck('id_usuario')->unique();

        $usuarios = User::whereIn('id', $userIds)->get()->keyBy('id');

        $logsConUsuarios = $logs->map(function ($log) use ($usuarios) {
            $usuario = $usuarios->get($log->id_usuario);
        
            if ($usuario) {
                $log->name = $usuario->name;
                $log->email = $usuario->email;
            } else {
                $log->name = null;
                $log->email = null;
            }
        
            return $log;
        });
        return response()->json(['data' => $logsConUsuarios, 'success' => true], 200);
    }
}

public function logsMethodPost() {
    $logs = Logs::where('method', 'POST')->get();

    if ($logs->isEmpty()) {
        return response()->json(['data' => [], 'success' => false], 400);
    } else {
        $userIds = $logs->pluck('id_usuario')->unique();

        $usuarios = User::whereIn('id', $userIds)->get()->keyBy('id');

        $logsConUsuarios = $logs->map(function ($log) use ($usuarios) {
            $usuario = $usuarios->get($log->id_usuario);
        
            if ($usuario) {
                $log->name = $usuario->name;
                $log->email = $usuario->email;
            } else {
                $log->name = null;
                $log->email = null;
            }
        
            return $log;
        });
        return response()->json(['data' => $logsConUsuarios, 'success' => true], 200);
    }
}


public function logsMethodPut() {
    $logs = Logs::where('method', 'PUT')->get();

    if ($logs->isEmpty()) {
        return response()->json(['data' => [], 'success' => false], 400);
    } else {
        $userIds = $logs->pluck('id_usuario')->unique();

        $usuarios = User::whereIn('id', $userIds)->get()->keyBy('id');

        $logsConUsuarios = $logs->map(function ($log) use ($usuarios) {
            $usuario = $usuarios->get($log->id_usuario);
        
            if ($usuario) {
                $log->name = $usuario->name;
                $log->email = $usuario->email;
            } else {
                $log->name = null;
                $log->email = null;
            }
        
            return $log;
        });
        return response()->json(['data' => $logsConUsuarios, 'success' => true], 200);
    }
}


public function logsMethodDelete() {
    $logs = Logs::where('method', 'DELETE')->get();

    if ($logs->isEmpty()) {
        return response()->json(['data' => [], 'success' => false], 400);
    } else {
        $userIds = $logs->pluck('id_usuario')->unique();

        $usuarios = User::whereIn('id', $userIds)->get()->keyBy('id');

        $logsConUsuarios = $logs->map(function ($log) use ($usuarios) {
            $usuario = $usuarios->get($log->id_usuario);
        
            if ($usuario) {
                $log->name = $usuario->name;
                $log->email = $usuario->email;
            } else {
                $log->name = null;
                $log->email = null;
            }
        
            return $log;
        });
        return response()->json(['data' => $logsConUsuarios, 'success' => true], 200);
    }
}

    public function filterLogsByMethod($num) {
        $methodMap = [
            0 => 'GET',
            1 => 'POST',
            2 => 'PUT',
            3 => 'DELETE',
            4 => 'PATCH'
        ];
    
        if (!array_key_exists($num, $methodMap)) {
            return response()->json(['error' => 'Número de método inválido.'], 400); 
        }
    
        $method = $methodMap[$num]; 
    
        $logs = Logs::where('method', $method)->get();
    
        if ($logs->isEmpty()) { 
            return response()->json(['data' => $logs, 'success' => false], 400);
        } else {
            return response()->json(['data' => $logs, 'success' => true], 200); 
        }
    }
    
    public function cleanlogs() {
        Logs::truncate();
    
        $count = Logs::count();
    
        if ($count == 0){
            return response()->json(["success" => true, "message" => "Todos los registros han sido eliminados."], 200);
        } else {
            return response()->json(["success" => false, "message" => "No se pudieron eliminar los registros."], 500);
        }
    }
    

  public function logsByName(String $userName) {
    // Buscar el usuario por su nombre
    $user = User::where('name', $userName)->first();

    if (!$user) {
        // Si el usuario no existe, devolver una respuesta de error
        return response()->json(['data' => [], 'success' => false], 400);
    }

    // Obtener los logs asociados al usuario encontrado
    $logs = Logs::where('id_usuario', $user->id)->get();

    if ($logs->isEmpty()) {
        // Si no hay logs asociados al usuario, devolver una respuesta de error
        return response()->json(['data' => [], 'success' => false], 400);
    }

    // Agregar información de nombre y email al resultado
    $logsConUsuarios = $logs->map(function ($log) use ($user) {
        $log->name = $user->name;
        $log->email = $user->email;
        return $log;
    });

    return response()->json(['data' => $logsConUsuarios, 'success' => true], 200);
}

    
}
