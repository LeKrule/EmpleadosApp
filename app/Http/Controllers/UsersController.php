<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function registrar(Request $req){
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);
        $user = new User;

        try{
                if (isset($Data->nombre)&& isset($Data->email)&& isset($Data->pass)&& isset($Data->photo)&& isset($Data->activo)){
                $user->nombre = $Data->nombre;
                $user->email = $Data->email;
                $user->pass = $Data->pass;
                $user->puesto = $Data->puesto;
                $user->activo = $Data->activo;
                $user->save();
                $response['msg'] = " el curso ha sido creado correctamente";
                $response['status'] = 1;
                }

            }catch (\Exception $error){
                $response['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
                $response['status'] = 0;
            }
            return response()->json($response);
            }
}
