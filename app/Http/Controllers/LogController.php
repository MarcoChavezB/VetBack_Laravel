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
        Sensor::truncate();
    
        $count = Sensor::count();
    
        if ($count == 0){
            return response()->json(["success" => true, "message" => "Todos los registros han sido eliminados."], 200);
        } else {
            return response()->json(["success" => false, "message" => "No se pudieron eliminar los registros."], 500);
        }
    }
    

    
}
