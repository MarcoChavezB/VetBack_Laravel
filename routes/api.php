<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VetAppointmentController;
use App\Http\Controllers\ServicesController;
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


/////////////////////////////// sin loguearse ////////////////////////////////////
Route::post('/store', [UserController::class, 'register']);

Route::post('/login', [UserController::class, 'login']);


Route::post('/verifyCode', [UserController::class, 'verifyCode'])
    ->name('Users.verifyCode');

Route::get('/getCode/{userId}', [UserController::class, 'getCode'])
    ->name('Users.getCode')
    ->where('userId', '[0-9]+');

Route::get('/users/index', [UserController::class, 'index']);
Route::get('/users/totalUsers', [UserController::class, 'totalUsers']);
Route::post('/users/desactivate/{id}', [UserController::class, 'desactivate'])
->where('id', '[0-9]+')
->name('Users.desactivate');

Route::post('/email/verify/code/{userId}', [EmailVerificationController::class, 'sendVerifyCodeEmail'])
    ->where('userId', '[0-9]+');


Route::get('/code/isActive/{userId}', [UserController::class, 'isCodeActive'])
    ->name('Users.isCodeActive')
    ->where('userId', '[0-9]+');


Route::prefix('/product')->group(function (){
    Route::get('/index', [ProductController::class, 'index']);
    Route::post('/store', [ProductController::class, 'store']);
    Route::delete('/delete/{id}', [ProductController::class, 'destroy'])->where('id', '[0-9]+');
    Route::put('/update/{id}', [ProductController::class, 'update'])->where('id', '[0-9]+');
    Route::get('/show/{id}', [ProductController::class, 'show'])->where('id', '[0-9]+');
    Route::get('/totalProducts', [ProductController::class, 'totalProducts']);
    Route::get('/stockBajo', [ProductController::class, 'stockBajo']);
    Route::post('/venta', [ProductController::class, 'realizarVenta']);
    Route::post('/getTotal', [ProductController::class, 'getTotal']);
});

Route::prefix('/category')->group(function (){
    Route::get('/index', [CategoryController::class, 'index']);
});


///////////////////////////////////////////////////////////

// MIDDLEWARES

// 'code.verified' ----- rutas para verificar que el codigo sea valido
// 'activeaccount.verified' ----- rutas para verificar que la cuenta sea activa, tambien ira a raiz de todas las rutas
// 'admin.auth' ----- rutas para solo administrador
// 'guest.auth' ----- rutas para solo invitado
// 'usuario.auth' ----- rutas para solo usuarios

///////////////////////////////////////////////////////////



Route::middleware(['auth:sanctum'])->group(function () { // verifica el token

    Route::get('/authenticatetoken', function () {
        return response()->json([
            'status' => true
        ]);
    });

    Route::get('/logout', [UserController::class, 'logout']);


    Route::middleware(['activeaccount.verified'])->group(function () { // verifica la cuenta activada

        Route::get('/activeaccount', function () {
            return response()->json([
                'status' => true
            ]);
        });

        Route::middleware(['code.verified'])->group(function () { // codigo verificado
        
            Route::get('/codeverified', function () {
                return response()->json([
                    'status' => true
                ]);
            });


                Route::middleware(['guest.auth'])->group(function () {
                    Route::get('/guestauth', function (Request $request) {
                        return response()->json(['msg' => 'bienvenido invitado']);
                    });
                });


                Route::middleware(['usuario.auth'])->group(function () {
                    Route::get('/userauth', function (Request $request) {
                        return response()->json(['msg' => 'bienvenido usuario']);
                    });
                });

                Route::middleware(['admin.auth'])->group(function () {
                    Route::get('/adminauth', function (Request $request) {
                        return response()->json(['msg' => 'bienvenido admin']);
                    });
                });

                Route::prefix('/products')->group(function () {
                    Route::get('/index', [ProductController:: class, 'index']);
                    Route::get('/index/disabled', [ProductController:: class, 'indexDisabled']);
                    Route::post('/store', [ProductController:: class, 'store']);
                    Route::delete('/delete/{id}', [ProductController:: class, 'destroy'])->where('id', '[0-9]+');
                    Route::post('/activate/{id}', [ProductController:: class, 'activateProd'])->where('id', '[0-9]+');
                    Route::put('/update/{id}', [ProductController:: class, 'update'])->where('id', '[0-9]+');
                });

                Route::prefix('/vetappointment')->group(function () {
                    Route::post('/store', [VetAppointmentController::class, 'store']);
                    Route::get('/index', [VetAppointmentController::class, 'index']);
                    Route::get('/canceled/index', [VetAppointmentController::class, 'cancelledIndex']);
                    Route::get('/completed/index', [VetAppointmentController::class, 'completedIndex']);
                    Route::put('/complete/{id}', [VetAppointmentController::class, 'markAsCompleted'])->where('id', '[0-9]+');
                    Route::put('/reject/{id}', [VetAppointmentController::class, 'markAsRejected'])->where('id', '[0-9]+');
                    Route::put('/reopen/{id}', [VetAppointmentController::class, 'reOpen'])->where('id', '[0-9]+');
                    Route::get('/user/{id}', [VetAppointmentController::class, 'getVetAppointmentsByUser'])->where('id', '[0-9]+');
                    Route::get('/totalApointments', [VetAppointmentController::class, 'totalApointments']);
                    Route::get('/info/Appointments', [VetAppointmentController::class, 'infoAppointments']);

                });

                Route::prefix('/pet')->group(function () {
                    Route::post('/store', [PetController::class, 'store']);
                    Route::get('/index/{id}', [PetController::class, 'userPets'])->where('id', '[0-9]+');
                    Route::get('/userpets/{id}', [PetController::class, 'userPets'])->where('id', '[0-9]+');
                    Route::get('/show/{id}', [PetController::class, 'show'])->where('id', '[0-9]+');
                    Route::put('/update/{id}', [PetController::class, 'update'])->where('id', '[0-9]+');
                    Route::delete('/delete/{id}', [PetController::class, 'destroy'])->where('id', '[0-9]+');
                    Route::put('/activate/{id}', [PetController::class, 'activate'])->where('id', '[0-9]+');
                    Route::get('/activatedPets', [PetController::class, 'index']);
                    Route::get('/deactivatedPets', [PetController::class, 'deactivatedPets'])->where('id', '[0-9]+');
                    Route::get('/active/name/{name}', [PetController::class, 'findActivePetByName'])->where('name', '[a-zA-Z\- ]+');
                    Route::get('/deactivated/name/{name}', [PetController::class, 'findDeactivatedPetByName'])->where('name', '[a-zA-Z\- ]+');
                });

                Route::prefix('/specie')->group(function () {
                    Route::post('/store', [SpecieController::class, 'store']);
                    Route::get('/index', [SpecieController::class, 'index']);
                    Route::get('/deactivated/index', [SpecieController::class, 'deactivatedIndex']);
                    Route::put('/activate/{id}', [SpecieController::class, 'activate'])->where('id', '[0-9]+');
                    Route::delete('/delete/{id}', [SpecieController::class, 'destroy'])->where('id', '[0-9]+');
                    Route::get('/active/name/{name}', [SpecieController::class, 'findActiveSpeciesByName'])->where('name', '[a-zA-Z\- ]+');
                    Route::get('/deactivated/name/{name}', [SpecieController::class, 'findDeactivatedSpeciesByName'])->where('name', '[a-zA-Z\- ]+');
                    Route::put('/update/{id}', [SpecieController::class, 'update'])->where('id', '[0-9]+');
                    Route::get('/show/{id}', [SpecieController::class, 'show'])->where('id', '[0-9]+');
                });

                Route::prefix('/vetprescription')->group(function (){
                    Route::post('/store', [VetPrescriptionController::class, 'store']);
                    Route::get('/index', [VetPrescriptionController::class, 'index']);
                    Route::get('/user/{id}', [VetPrescriptionController::class, 'getUserPrescriptions'])->where('id', '[0-9]+');
                });

                Route::prefix('/services')->group(function (){
                    Route::get('/index', [ServicesController::class, 'index']);
                    Route::get('/show/{id}', [ServicesController::class, 'show'])->where('id', '[0-9]+');
                    Route::post('/store', [ServicesController::class, 'store']);
                    Route::put('/update', [ServicesController::class, 'update']);
                    Route::delete('/destroy/{id}', [ServicesController::class, 'destroy'])->where('id', '[0-9]+');
                });
            });

        });

});
Route::post('/r', [UserController::class, 'insert']);
