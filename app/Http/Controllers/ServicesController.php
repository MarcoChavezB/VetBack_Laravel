<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;
use App\Events\ServiceEvent;

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

        if(!$service){
            return response()->json(['status' => false], 400);
        }

        $service->delete();

        return response()->json(['status' => true], 204);
    }

    public function sse($message = "Se elimino un servicio"){

        if (connection_status() != CONNECTION_NORMAL){
            return;
        }


        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        try {
            echo 'data:' . json_encode($message) . "\n\n";

            ob_flush();
            flush();
        } catch (\Exception $e) {
            echo 'data:' . json_encode($e->getMessage()) . "\n\n";
            ob_flush();
            flush();
        }
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
