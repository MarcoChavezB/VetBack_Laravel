<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Specie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SpecieController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'specie_name' => 'required|string|max:100|unique:species,specie_name'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors"=> $validator->errors()], 400);
        }

        $specie = new Specie();
        $specie->specie_name = $request->specie_name;
        $specie->save();

        return response()->json(["success" => true, "message"=>"Especie registrada correctamente"], 201);
    }

    public function update(Request $request, $id){
        $specie = Specie::find($id);
        if (!$specie) {
            return response()->json(["success" => false, 'message' => 'Especie no encontrada'], 400);
        }
        $validator = Validator::make($request->all(), [
            'specie_name' => 'required|string|max:100|unique:species,specie_name,'.$id
        ]);

        if ($validator->fails()) {
            return response()->json(["errors"=> $validator->errors()], 400);
        }

        $specie->specie_name = $request->specie_name;
        $specie->save();

        return response()->json(["success" => true, "message"=>"Especie actualizada correctamente"], 200);
    }

    public function index(){
        $species = Specie::where('is_active', true)->get();
        if ($species->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay especies activas registradas'], 400);
        }
        return response()->json(['species' => $species], 200);
    }

    public function deactivatedIndex(){
        $species = Specie::where('is_active', false)->get();
        if ($species->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay especies desactivadas registradas'], 400);
        }
        return response()->json(['species' => $species], 200);
    }

    public function destroy($id){
        $specie = Specie::find($id);
        if (!$specie) {
            return response()->json(["success" => false, 'message' => 'Especie no encontrada'], 400);
        }
        $specie->is_active = false;
        $specie->save();

        $pets = Pet::where('specie_id', $id)->get();
        foreach ($pets as $pet) {
            $pet->is_active = false;
            $pet->save();
        }

        return response()->json(["success" => true, "message"=>"Especie y sus mascotas han sido desactivadas correctamente"], 200);
    }


    public function activate($id){
        $specie = Specie::find($id);
        if (!$specie) {
            return response()->json(["success" => false, 'message' => 'Especie no encontrada'], 400);
        }
        $specie->is_active = true;
        $specie->save();

        $pets = Pet::where('specie_id', $id)->get();
        foreach ($pets as $pet) {
            $pet->is_active = true;
            $pet->save();
        }

        return response()->json(["success" => true, "message"=>"Especie y sus mascotas han sido activadas correctamente"], 200);
    }

    public function findActiveSpeciesByName($name){
        $species = Specie::where('is_active', true)->where('specie_name', 'like', '%'.$name.'%')->get();
        if ($species->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay especies activas registradas'], 400);
        }
        return response()->json(['species' => $species], 200);
    }

    public function findDeactivatedSpeciesByName($name){
        $species = Specie::where('is_active', false)->where('specie_name', 'like', '%'.$name.'%')->get();
        if ($species->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay especies desactivadas registradas'], 400);
        }
        return response()->json(['species' => $species], 200);
    }

    public function show($id){
        $specie = Specie::find($id);
        if (!$specie) {
            return response()->json(["success" => false, 'message' => 'Especie no encontrada'], 400);
        }

        $specie = DB::table('species')
            ->select('species.*')
            ->where('species.id', $id)
            ->get();

        return response()->json(['specie' => $specie], 200);
    }

}
