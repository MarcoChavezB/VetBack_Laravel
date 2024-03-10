<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
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

}
