<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VetAppointmentController;
use App\Http\Controllers\VetPrescriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/authenticate', function (Request $request) {
    return response()->json(['error' => 'Token inválido'], 401);

})->name('error');

Route::post('/store', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/authenticatetoken', function () {
        return response()->json([
            'status' => true
        ]);
    });

    Route::get('/logout', [UserController::class, 'logout']);

});


Route::post('/pet/store', [PetController::class, 'store']);
Route::get('/pet/index/{id}', [PetController::class, 'getPetsByUser'])->where('id', '[0-9]+');
Route::post('/specie/store', [SpecieController::class, 'store']);
Route::get('/specie/index', [SpecieController::class, 'index']);
Route::post('/vetappointment/store', [VetAppointmentController::class, 'store']);
Route::post('/vetprescription/store', [VetPrescriptionController::class, 'store']);
Route::get('/vetappointment/index', [VetAppointmentController::class, 'index']);
Route::get('/vetappointment/show/{id}', [VetAppointmentController::class, 'show'])->where('id', '[0-9]+');
Route::put('/vetappointment/complete/{id}', [VetAppointmentController::class, 'markAsCompleted'])->where('id', '[0-9]+');
Route::put('/vetappointment/reject/{id}', [VetAppointmentController::class, 'markAsRejected'])->where('id', '[0-9]+');


Route::post('/verifyCode', [UserController::class, 'verifyCode'])
    ->name('Users.verifyCode');

Route::get('/getCode/{userId}', [UserController::class, 'getCode'])
    ->name('Users.getCode')
    ->where('userId', '[0-9]+');

Route::post('/email/verify/code/{userId}', [EmailVerificationController::class, 'sendVerifyCodeEmail'])
    ->name('EmailVerification.sendVerifyCodeEmail')
    ->where('userId', '[0-9]+');


Route::get('/code/isActive/{userId}', [UserController::class, 'isCodeActive'])
    ->name('Users.isCodeActive')
    ->where('userId', '[0-9]+');

Route::post('/r', [UserController::class, 'r']);