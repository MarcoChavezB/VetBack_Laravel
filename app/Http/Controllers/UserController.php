<?php

namespace App\Http\Controllers;
use App\Events\NotifyEvent;
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

    function changeRole($id){
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
        event(new NotifyEvent($user->role, $user->id));
        return response()->json(['mensaje' => 'rol cambiado exitosamente']);
    }

<<<<<<< HEAD
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ],
        [
            'email.required' => 'El email es requerido',
            'email.email' => 'El email debe ser un email válido',
            'password.required' => 'El password es requerido',
            'password.min' => 'El password debe tener al menos 8 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'success' => false,
                'msg' => 'Usuario no encontrado'
            ], 404);
        }

        if(! $user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'success' => false,
                'msg' => 'Contraseña incorrecta'
            ], 401);
        }

        if($user->email_verified == false){
            return response()->json([
                'success' => false,
                'msg' => 'Correo no verificado'
            ], 403);
        }

        if($user->account_active == false){
            return response()->json([
                'msg' => 'Cuenta no activada'
            ], 403);
        }


        if($request->has('codigo')) { 

            if (is_null($user->{'2fa_code'}) || is_null($user->{'2fa_code_at'})) {
                return response()->json(['msg' => 'Aun no generas un código de verificación.', 'data' => $user->email], 401);
            }

            $minutosParaExpiracion = 5;

            $codigoValido = Carbon::now()->diffInMinutes($user->{'2fa_code_at'}) <= $minutosParaExpiracion;

            if (!$codigoValido) {
                return response()->json(['msg' => 'Codigo expirado.', 'data' => $user->email], 403);
            }

            if($this->verifyCode($request->codigo, $user)){
                $token = $user->createToken('Accesstoken')->plainTextToken;

                return response()->json([
                    'msg' => 'Se ha logeado correctamente',
                    'data' => $user,
                    'jwt' => $token,
                    'token_type' => 'Bearer',
                ]);

            } else {
                return response()->json(['msg' => 'Codigo incorrecto.', 'data' => $user->email], 401);
            }
           
        } else {
            $this->getCode($user->id);
            return response()->json(['msg' => 'Código 2FA enviado. Por favor, verifícalo.', 'data' => $user->email], 201);
        }
       
    }

   
=======

>>>>>>> c27eac7f9d9f37da834c5c22bc93529d7ce6124c

    function getCode($userId){
        if(!$userId){
            return response()->json(['mensaje' => 'parametro no valido'], 404);
        }

        $user = User::find($userId);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $codigo = random_int(100000, 999999);
        $user->{'2fa_code'} = encrypt($codigo);
        $user->{'2fa_code_at'} = Carbon::now();
        $user->save();

        $this->sendVerifyCodeEmail($user->email, $codigo);

    }


    function sendVerifyCodeEmail($email, $codigo){

        try {
            Mail::to($email)->send((new EmailCodeVerification($codigo))->build());
        }catch (\Exception $e){
            return response()->json(["success" => false, "message" => "Ha ocurrido un error interno."], 500);
        }

    }
    
    function isCodeActive($userId){
        $user = User::find($userId);
        if(!$user){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }

        $minutosParaExpiracion = 5;

        $codigoValido = Carbon::now()->diffInMinutes($user->{'2fa_code_at'}) <= $minutosParaExpiracion;

        if (!$codigoValido) {
            return response()->json(['mensaje' => 'El código ha expirado'], 400);
        }

        return response()->json(['mensaje' => 'El código sigue siendo válido']);
    }


    function verifyCode($codigo_ingresado, $user) {

        if (hash_equals((string)decrypt($user->{'2fa_code'}), (string)$codigo_ingresado)) {
            $user->{'2fa_code'} = null;
            $user->{'2fa_code_at'} = null;
            $user->save();
            return true;
        }

        return false;
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
    

    public function logout(){
        $user = Auth::user();

        if (!$user) {
            return response()->json(['msg' => 'Usuario no encontrado'], 404);
        };

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
