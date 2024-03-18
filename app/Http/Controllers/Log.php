<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logs;
class Log extends Controller
{
    function getlogs(){
        $logs = Logs::All()
        if ($logs){
            return response()->json('data'=>$logs);
        }
    }
}
