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


Route::prefix('users')->group(function(){
    Route::middleware(['auth-api', 'auth-role'])->put('registrar', [UsersController::class, 'registrar']);
    Route::middleware(['auth-api', 'auth-role'])->get('detalle/{id}', [UsersController::class, 'DetallesUsuario']);
    Route::middleware(['auth-api', 'auth-role'])->put('listar', [UsersController::class, 'listar']);
    Route::middleware(['auth-api', 'auth-role'])->put('consultar', [UsersController::class, 'consultar']);
    Route::middleware(['auth-api', 'auth-role'])->put('perfil', [UsersController::class, 'perfil']);
    Route::middleware(['auth-api', 'auth-role'])->put('editar', [UsersController::class, 'editar']);
    Route::middleware(['auth-api'])->get('perfil', [UsersController::class, 'perfil']);
    Route::get('PassRecovery', [UsersController::class, 'RecuperarPass']);
});
