<?php

namespace App\Http\Controllers;

use App\Events\AppointmentStored;
use App\Models\VetAppointment;
use App\Rules\AppointmentTime;
use App\Rules\UniqueDateTimeWithGap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VetAppointmentController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
            'appointment_date' => ['required', 'date', new AppointmentTime],
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

        event(new AppointmentStored(['msg' => 'Nueva cita registrada']));
        

        return response()->json(["success" => true, "message"=>"Cita registrada correctamente"], 201);
    }

    public function index(){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('vet_appointments.status', 'Abierta')
            ->where('pets.is_active', true)
            ->latest('vet_appointments.created_at')
            ->take(20)
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas registradas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    public function completedIndex(){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('vet_appointments.status', 'Consultada')
            ->latest('vet_appointments.created_at')
            ->take(20)
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas registradas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    public function cancelledIndex(){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('vet_appointments.status', 'Rechazada')
            ->latest('vet_appointments.created_at')
            ->take(20)
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas rechazadas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
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

    public function reOpen($id){
        $vetAppointment = VetAppointment::find($id);
        if (!$vetAppointment) {
            return response()->json(['message' => 'Cita no encontrada'], 404);
        }
        $vetAppointment->status = 'Abierta';
        $vetAppointment->save();
        return response()->json(['success' => true, 'message' => 'Cita reabierta'], 200);
    }

    public function getVetAppointmentsByUser($id){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*',  'pets.name as pet')
            ->where('vet_appointments.user_id', $id)
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No hay citas registradas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    function totalApointments(){
        $totalApointments = VetAppointment::all()->count();
        return response()->json(['total' => $totalApointments], 200);
    }

    function infoAppointments(){
        $infoAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->get();
        return response()->json(['info' => $infoAppointments], 200);
    }

    public function findByName($name){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('users.name', 'like', '%'.$name.'%')
            ->where('vet_appointments.status', 'Abierta')
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas registradas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    public function findCancelledByName($name){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('users.name', 'like', '%'.$name.'%')
            ->where('vet_appointments.status', 'Rechazada')
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas rechazadas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    public function findCompletedByName($name){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('users.name', 'like', '%'.$name.'%')
            ->where('vet_appointments.status', 'Consultada')
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas completadas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }

    public function findUserAppointmentsByDate($date, $id){
        $vetAppointments = DB::table('vet_appointments')
            ->join('users', 'vet_appointments.user_id', '=', 'users.id')
            ->join('pets', 'vet_appointments.pet_id', '=', 'pets.id')
            ->select('vet_appointments.*', 'users.name as user', 'pets.name as pet')
            ->where('vet_appointments.user_id', $id)
            ->whereDate('vet_appointments.appointment_date', $date)
            ->get();

        if ($vetAppointments->isEmpty()) {
            return response()->json(['success' => false,'message' => 'No hay citas registradas'], 400);
        }

        return response()->json(['vet_appointments' => $vetAppointments], 200);
    }
}
