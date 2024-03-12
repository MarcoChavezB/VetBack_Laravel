<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Mail\EmailVerification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{

    function index()
    {
        $users = User::where('role', 'guest')
                     ->orWhere('role', 'user')
                     ->get();
        return response()->json([
            "Users" => $users
        ]);
    }

    function desactivate($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        $user->account_active = false;
        $user->save();
        return response()->json(['mensaje' => 'Usuario desactivado']);
    }

    
    function getCode($userId){
        $codigo = Str::random(6);
        $hashedCode = hash('sha256', $codigo);
        Cache::put('codigo_' . $userId, $hashedCode, Carbon::now()->addMinutes(1));
        return $codigo;
    }
    
    function isCodeActive($userId){
        $user = User::find($userId);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        return response()->json(['isActive' => $user->code_verified]);
    }
    
    function verifyCode(Request $request) {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|min:6|max:6',
            'userId' => 'required|integer'
        ],
        [
            'codigo.required' => 'El código es requerido',
            'codigo.min' => 'El código debe tener 6 caracteres',
            'codigo.max' => 'El código debe tener 6 caracteres',
            'userId.required' => 'El userId es requerido',
            'userId.integer' => 'El userId debe ser un número entero'
        ]);
    
        $validatorErrors = $validator->errors()->toArray();
    
        if ($validator->fails()) {
            return response()->json([
                'error' => $validatorErrors
            ], 400);
        }
    
        $usuario_id = $request->userId;
        $codigo_ingresado = $request->codigo;
    
        if (!User::find($usuario_id)) {
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
    
        if (!Cache::has('codigo_' . $usuario_id)) {
            return response()->json(['mensaje' => 'Código no válido'], 400);
        }
    
        $hashedCode = Cache::get('codigo_' . $usuario_id);
    
        if (hash_equals($hashedCode, hash('sha256', $codigo_ingresado))) {
            Cache::forget('codigo_' . $usuario_id);
            $user = User::find($usuario_id);
            $user->code_verified = true;
            $user->save();
            return response()->json(['mensaje' => 'Código válido']);
        }
    
        return response()->json(['mensaje' => 'Código inválido'], 400);
    }
        



    public function register(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ],
        [
            'name.required' => 'El nombre es requerido',
            'name.min' => 'El nombre debe tener al menos 3 caracteres',
            'name.max' => 'El nombre debe tener máximo 255 caracteres',
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser un email válido',
            'email.unique' => 'El email ya está en uso',
            'password.required' => 'El password es requerido',
            'password.min' => 'El password debe tener al menos 8 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();

        Mail::to($user->email)->send(new EmailVerification($user));

        return response()->json([
            'message' => 'User created successfully'
        ], 201);
    }

    public function login(Request $request){

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if(! $user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'msg' => 'Contraseña incorrecta'
            ], 401);
        }

        if($user->email_verified == false){
            return response()->json([
                'msg' => 'Correo no verificado'
            ], 403);
        }

        $token = $user->createToken('Accesstoken')->plainTextToken;

        return response()->json([
            'msg' => 'Se ha logeado correctamente',
            'data' => $user,
            'jwt' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['msg' => 'Usuario no encontrado'], 404);
        };
        $userFind = User::find($user->id);
        $userFind->code_verified = false;
        $userFind->save();
        $user->currentAccessToken()->delete();        
        return response()->json(['status' => true]);
    }
}
