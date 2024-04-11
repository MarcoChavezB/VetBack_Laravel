<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use App\Events\ServiceEvent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        event(new ServiceEvent(['message' => 'hay un nuevo servicio']));

        return response()->json($service);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $service = Service::find($request->id);

        if(!$service){
            return response()->json(['mensaje' => 'Servicio no encontrado'], 400);
        }

        $service->update($validator->validated());

        return response()->json($service);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
    
        if (!$service) {
            return response()->json(['status' => false, 'error' => 'Service not found'], 400);
        }
    
        $service->delete();
        Cache::put('Evento', "true", 10);

        return response()->json(['status' => true, 'message' => 'Service deleted'], 200);
    }
    

    public function sse() {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');
        header('Access-Control-Allow-Origin: *');
    
        if(Cache::has('Evento')) {
            echo "data: " . json_encode(true) . "\n\n";
            ob_flush();
            flush();
        }
        else {
            echo ": " . "\n\n";
            ob_flush();
            flush();
        }
        sleep(5);
    }
    

    public function index()
    {
        $services = Service::all();
        return response()->json(['services' => $services, 'status' => true] );
    }

    public function show($id)
    {
        $service = Service::find($id);
        if(!$service){
            return response()->json(['mensaje' => 'Servicio no encontrado'], 400);
        }
        return response()->json($service);
    }
}
