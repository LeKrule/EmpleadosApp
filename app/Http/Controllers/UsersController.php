<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function login(Request $req) {
        $respuesta = ['status'=> 1, 'msg'=>''];
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);

        try{
            if($Data->email) {
                $user = User::where('email', $Data->email)->first();
                if($Data->email) {
                    if(Hash::check($Data->password, $user->password)) {
                        $token = Hash::make(now());
                        $user->api_token = $token;
                        $user->save();
                        $respuesta['msg'] = "Sesion iniciada. Token: ".$token;
                    } else {
                        $respuesta['msg'] = "La contraseña no coincide";
                        $respuesta['status'] = 0;
                    }
                } else {
                    $respuesta['msg'] = "El usuario introducido no existe";
                    $respuesta['status'] = 0;
                }
            } else {
                $respuesta['msg'] = "Debes introducir un email";
                $respuesta['status'] = 0;
            }

        }catch (\Exception $error){
            $respuesta['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
            $respuesta['status'] = 0;
        }
        return response()->json($respuesta);
    }

    public function registrar(Request $req){
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);
        $user = new User();

        try{
            $validator = Validator::make(json_decode($JsonData, true), [
                'nombre' => 'required',
                'email' => 'required|unique:users',
                'password' => 'required',
                'puesto' => 'required|in:directivo,rrhh,empleado',
                'salario' => 'required',
            ]);

            if($validator->fails()){
                $respuesta = ['status'=>0, 'msg'=>$validator->errors()->first()];
            } else {
                $user->nombre = $Data->nombre;
                $user->email = $Data->email;
                $user->password = $Data->password;
                $user->puesto = $Data->puesto;
                $user->salario = $Data->salario;
                $user->save();
                $respuesta['msg'] = " el curso ha sido creado correctamente";
                $respuesta['status'] = 1;
            }

        }catch (\Exception $error){
            $respuesta['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
            $respuesta['status'] = 0;
        }
        return response()->json($respuesta);
    }
    public function registrarauto(){

        try{
            $user = new User();
            $user->nombre = 'Adrian';
            $user->email = 'adrian@gmail.com';
            $user->password = Hash::make('password');
            $user->puesto = 'directivo';
            $user->salario = '100000';
            $user->biografia = 'Hola soy directivo e inutil, 100k';
            $user->save();
            $respuesta['msg'] = "el usuario se ha creado correctamente";
            $respuesta['status'] = 1;
        }catch (\Exception $error){
            $respuesta['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
            $respuesta['status'] = 0;
        }
        return response()->json($respuesta);
    }


}
