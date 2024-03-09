<?php

namespace App\Http\Controllers;

use App\Models\EmailCodeVerification;
use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\UserController;
use App\Mail\EmailCodeVerification as MailEmailCodeVerification;

class EmailVerificationController extends Controller
{
    private $userController;
    public function __construct()
    {
        $this->userController = new UserController();
    }

    public function verify(Request $request, Int $user_id){
        if (!$request->hasValidSignature()) {
            return view('emails.EmailVerificationError');
        }

        $user = User::find($user_id);

        if (!$user){
            return view('emails.FindUserError');
        }
        $token = $request->token;

        $hashedToken = hash('sha256', $token);

        $token = EmailVerificationToken::where('token', hash('sha256', $hashedToken))->first();

        if (!!$token->valid) {
            return view('emails.TokenError');
        }

        $token->valid = false;
        $token->save();

        $user->email_verified = true;
        $user->email_verified_at = now();
        $user->save();

        return view('emails.EmailVerificationSuccess');
    }

    function sendVerifyCodeEmail($userId){
        if (!User::find($userId)){
            return response()->json(['mensaje' => 'Usuario no encontrado'], 404);
        }
        if(User::find($userId)->code_verified){
            return response()->json(['mensaje' => 'El usuario ya ha sido verificado'], 400);
        }
        $user = User::find($userId);
        $codigo = $this->userController->getCode($userId);
        $email = $user->email;
        Mail::to($email)->send((new MailEmailCodeVerification($codigo))->build());
    }
}
