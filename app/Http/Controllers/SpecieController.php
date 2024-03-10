<?php

namespace App\Http\Controllers;

use App\Models\Specie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SpecieController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'specie_name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(["errors"=> $validator->errors()], 400);
        }

        $specie = new Specie();
        $specie->specie_name = $request->specie_name;
        $specie->save();

        return response()->json(["success" => true, "message"=>"Especie registrada correctamente"], 201);

    }
}
