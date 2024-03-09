<?php

namespace App\Http\Controllers;

use App\Models\EmailVerificationToken;
use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function verify_email(Request $request, Int $user_id){
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
}
