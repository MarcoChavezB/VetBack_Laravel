<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;
class LogController extends Controller
{
    public function getLogs() {
        $logs = Logs::all(); 
    
        if ($logs->isEmpty()) { 
            return response()->json(['data' => $logs, 'success' => false], 400);
        } else {
            return response()->json(['data' => $logs, 'success' => true], 200); 
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
    

    
}
