<?php

namespace App\Http\Controllers;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

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
