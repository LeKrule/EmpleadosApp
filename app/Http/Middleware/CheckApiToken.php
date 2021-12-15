<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class CheckApiToken
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
        $token = $request->input('api_token');
        if($token) {
            $usuario = User::where('api_token')->first();
            if($usuario) {
                $request->usuario = $usuario;
                return $next($request);
            } else {
                return response('El api_token no pertenece a nadie', 401);
            }
        } else {
            return response('El api_token introducido no es valido', 401);
        }
    }
}
