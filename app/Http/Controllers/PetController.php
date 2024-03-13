<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'gender' => 'required|in:Macho,Hembra',
            'specie_id' => 'required|exists:species,id',
            'user_id' => 'required|exists:users,id'
            ]);

        if ($validator->fails()) {
            return response()->json(["errors"=> $validator->errors()], 400);
        }

        $pet = new Pet();
        $pet->name = $request->name;
        $pet->gender = $request->gender;
        $pet->specie_id = $request->specie_id;
        $pet->user_id = $request->user_id;
        $pet->save();

        return response()->json(["success" => true, "message"=>"Mascota registrada correctamente"], 201);
    }

    public function getPetsByUser($id){
        $pets = Pet::where('user_id', $id)->get();
        if ($pets->isEmpty()) {
            return response()->json(['message' => 'No hay mascotas registradas'], 404);
        }
        return response()->json(['pets' => $pets], 200);
    }

    public function userPets($id){

        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        $pets = Pet::where('user_id', $id)->get();
        if ($pets->isEmpty()) {
            return response()->json(['message' => 'No hay mascotas registradas'], 404);
        }
        $pets = DB::table('pets')
            ->join('species', 'pets.specie_id', '=', 'species.id')
            ->select('pets.*', 'species.specie_name as specie')
            ->where('pets.user_id', $id)
            ->get();
        return response()->json(['pets' => $pets], 200);

    }

}
