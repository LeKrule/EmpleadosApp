<?php

use App\Http\Controllers\UsersController;
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
Route::put('login', [UsersController::class, 'login']);
Route::get('registrar-auto', [UsersController::class, 'registrarauto']);

Route::middleware('auth-api')->group(function (){
    Route::prefix('users')->group(function(){
        Route::get('registrar', [UsersController::class, 'registrar']);
        Route::get('Olvidar-pass', [UsersController::class, 'RecuperarPass']);
        Route::get('detalle/{id}', [UsersController::class, 'DetallesUsuario']);
        Route::get('perfil', [UsersController::class, 'perfil']);
        Route::get('editar', [UsersController::class, 'editar']);

       //});


    });
});
