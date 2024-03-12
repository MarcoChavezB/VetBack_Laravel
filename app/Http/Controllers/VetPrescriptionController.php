<?php

namespace App\Http\Controllers;

use App\Models\VetPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VetPrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'diagnosis' => 'required|string|max:100',
            'observations' => 'required|string|max:255',
            'indications' => 'required|string|max:255',
            'vet_id' => 'required|exists:users,id',
            'vet_appointment_id' => 'required|exists:vet_appointments,id'
        ]);

        if ($validator->fails()) {
            return response()->json(["errors" => $validator->errors()], 400);
        }

        $vetPrescription = new VetPrescription();
        $vetPrescription->diagnosis = $request->diagnosis;
        $vetPrescription->observations = $request->observations;
        $vetPrescription->indications = $request->indications;
        $vetPrescription->vet_id = $request->vet_id;
        $vetPrescription->vet_appointment_id = $request->vet_appointment_id;
        $vetPrescription->save();

        return response()->json(["success" => true, "message" => "Receta registrada correctamente"], 201);
    }
}
