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
use App\Http\Controllers\EmailVerificationController;
use App\Mail\EmailCodeVerification;

class UserController extends Controller
{
    function index(){
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
        if ($user->account_active == true){
            $user->account_active = false;
        } else {
            $user->account_active = true;
        }
        $user->save();
        return response()->json(['mensaje' => 'Cambiado de status exitosamente ']);
    }

    function changerole($id){
        $user = User::find($id);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        if ($user->role == 'guest'){
            $user->role = 'user';
        } else {
            $user->role = 'guest';
        }
        $user->save();
        return response()->json(['mensaje' => 'Cambiado de role exitosamente ']);
    }
    function getCode($userId){
        if(!$userId){
            return response()->json(['mensaje' => 'paramtero no valido'], 404);
        }

        if(!User::find($userId)){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        $codigo = random_int(100000, 999999); 
        $hashedCode = hash('sha256', $codigo);
        $user = User::find($userId);
        $user->code = $hashedCode;
        $user->save();
        Cache::put('codigo_' . $userId, $hashedCode, Carbon::now()->addMinutes(1));
        return $codigo;
    }
    function isCodeActive($userId){
        $user = User::find($userId);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        if($user->code_verified == 0){
            $this->sendVerifyCodeEmail($userId);
            return response()->json([
                "message" => "El código ha sido enviado al correo del usuario"
            ]);
        }
        return response()->json(['isActive' => $user->code_verified]);
    }
    function sendVerifyCodeEmail($userId){
        if (!User::find($userId)){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        if(User::find($userId)->code_verified){
            return response()->json(['mensaje' => 'El usuario ya ha sido verificado'], 400);
        }
        $user = User::find($userId);
        $codigo = $this->getCode($userId);
        $email = $user->email;
        Mail::to($email)->send((new EmailCodeVerification($codigo))->build());
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

        $user = User::where('email', $request->email)->active()->first();

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
    public function logout(){
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
    function totalUsers(){
        $users = User::all();
        return response()->json([
            "total" => $users->count()
        ]);
    }

    function insert(){
        $user = new User();
        $user->name = "marco";
        $user->email = "ekectromagicyt@gmail.com";
        $user->email_verified = true;
        $user->code_verified = true;
        $user->account_active = true;
        $user->role = 'admin';
        $user->email_verified_at = now();
        $user->password = Hash::make('password123');
        $user->save();
        return response()->json([
            "mensaje" => "Usuario creado"
        ]);
    }

    function forid($id){
        $userFind = User::find($id);
        if (!$userFind) {
            return response()->json(['msg' => 'Usuario no encontrado'], 400);
        };
        return response()->json($userFind);
    }
}
