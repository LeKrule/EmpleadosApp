<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $usuario = $request->usuario;
        if($usuario) {
            if($usuario->role == 'directivo' || $usuario->role == 'rrhh') {
                return $next($request);
            } else {
                return response('No puedes acceder', 401);
            }
        } else {
            return response('No hay un usuario valido', 401);       //Nunca va a fallar, si eso falla en el CheckApiToken
        }
    }
}
