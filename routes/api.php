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

Route::middleware('Checkear_Usuario')->group(function (){
    Route::prefix('users')->group(function(){
        Route::get('login', [UsersController::class, 'login']);
        Route::get('registrar', [UsersController::class, 'registrar'])->withoutMiddleware("Checkear_Usuario");
        Route::get('Olvidar-pass', [UsersController::class, 'RecuperarPass'])->withoutMiddleware('Checkear_Usuario');
        Route::get('detalle/{id}', [UsersController::class, 'DetallesUsuario']);
        Route::get('perfil', [UsersController::class, 'perfil']);
        Route::get('editar', [UsersController::class, 'editar']);

       //});


    });
});
