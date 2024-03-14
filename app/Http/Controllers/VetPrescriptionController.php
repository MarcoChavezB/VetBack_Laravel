<?php

namespace App\Http\Controllers;

use App\Models\VetPrescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function index()
    {
        $vetPrescriptions = VetPrescription::all();
        if ($vetPrescriptions->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay recetas registradas'], 400);
        }

        $vetPrescriptions = DB::table('vet_prescriptions')
            ->join('users as vet_users', 'vet_prescriptions.vet_id', '=', 'vet_users.id')
            ->join('vet_appointments', 'vet_prescriptions.vet_appointment_id', '=', 'vet_appointments.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->join('users as pet_users', 'pets.user_id', '=', 'pet_users.id')
            ->select('vet_prescriptions.*', 'vet_users.name as vet','pet_users.name as client','pets.name as pet', 'vet_appointments.appointment_date as appointment_date')
            ->latest('vet_prescriptions.created_at')
            ->take(20)
            ->get();

        return response()->json(['prescriptions' => $vetPrescriptions], 200);
    }

    public function getUserPrescriptions($id)
    {
        $vetPrescriptions = DB::table('vet_prescriptions')
            ->join('users as vet_users', 'vet_prescriptions.vet_id', '=', 'vet_users.id')
            ->join('vet_appointments', 'vet_prescriptions.vet_appointment_id', '=', 'vet_appointments.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->join('users as pet_users', 'pets.user_id', '=', 'pet_users.id')
            ->select('vet_prescriptions.*', 'vet_users.name as vet','pets.name as pet', 'vet_appointments.appointment_date as appointment_date')
            ->where('pet_users.id', $id)
            ->get();

        if ($vetPrescriptions->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay recetas registradas'], 400);
        }

        return response()->json(['prescriptions' => $vetPrescriptions], 200);
    }
}
