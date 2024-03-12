<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProductController;
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

Route::prefix('/vetappointment')->group(function () {
    Route::post('/store', [VetAppointmentController::class, 'store']);
    Route::get('/index', [VetAppointmentController::class, 'index']);
    Route::get('/canceled/index', [VetAppointmentController::class, 'cancelledIndex']);
    Route::get('/completed/index', [VetAppointmentController::class, 'completedIndex']);
    Route::put('/complete/{id}', [VetAppointmentController::class, 'markAsCompleted'])->where('id', '[0-9]+');
    Route::put('/reject/{id}', [VetAppointmentController::class, 'markAsRejected'])->where('id', '[0-9]+');
    Route::put('/reopen/{id}', [VetAppointmentController::class, 'reOpen'])->where('id', '[0-9]+');
});

Route::prefix('/pet')->group(function () {
    Route::post('/store', [PetController::class, 'store']);
    Route::get('/index/{id}', [PetController::class, 'getPetsByUser'])->where('id', '[0-9]+');
});

Route::prefix('/specie')->group(function () {
    Route::post('/store', [SpecieController::class, 'store']);
    Route::get('/index', [SpecieController::class, 'index']);
});

Route::prefix('/vetprescription')->group(function (){
    Route::post('/store', [VetPrescriptionController::class, 'store']);
});



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
Route::get('/products/index', [ProductController:: class, 'index']);
Route::post('/products/store', [ProductController:: class, 'store']);
Route::delete('/products/delete/{id}', [ProductController:: class, 'destroy'])->where('id', '[0-9]+');
Route::put('/products/update/{id}', [ProductController:: class, 'update'])->where('id', '[0-9]+');
