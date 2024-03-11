<?php

namespace App\Http\Controllers;

use App\Models\VetAppointment;
use App\Rules\UniqueDateTimeWithGap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VetAppointmentController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
            'appointment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(["errors"=> $validator->errors()], 400);
        }

        $vetAppointment = new VetAppointment();
        $vetAppointment->appointment_date = $request->appointment_date;
        $vetAppointment->reason = $request->reason;
        $vetAppointment->pet_id = $request->pet_id;
        $vetAppointment->user_id = $request->user_id;
        $vetAppointment->save();

        return response()->json(["success" => true, "message"=>"Cita registrada correctamente"], 201);
    }

    public function index(){
        $vetAppointments = VetAppointment::all();
        if ($vetAppointments->isEmpty()) {
            return response()->json(['message' => 'No hay citas registradas'], 404);
        }
        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    public function show($id){
        $vetAppointment = VetAppointment::find($id);
        if (!$vetAppointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        return response()->json(['vet_appointment' => $vetAppointment], 200);
    }

    public function markAsCompleted($id){
        $vetAppointment = VetAppointment::find($id);
        if (!$vetAppointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        $vetAppointment->status = 'Consultada';
        $vetAppointment->save();
        return response()->json(['success' => true, 'message' => 'Cita marcada como completada'], 200);
    }

    public function markAsRejected($id){
        $vetAppointment = VetAppointment::find($id);
        if (!$vetAppointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        $vetAppointment->status = 'Rechazada';
        $vetAppointment->save();
        return response()->json(['success' => true, 'message' => 'Cita marcada como rechazada'], 200);
    }
}
