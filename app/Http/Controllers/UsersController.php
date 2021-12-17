<?php

namespace App\Http\Controllers;

use App\Mail\PassRecovery;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function login(Request $req) {
        $response = ['status'=> 1, 'msg'=>''];
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
                        $response['msg'] = "Sesion iniciada. Token: ".$token;
                    } else {
                        $response['msg'] = "La contraseña no coincide";
                        $response['status'] = 0;
                    }
                } else {
                    $response['msg'] = "El usuario introducido no existe";
                    $response['status'] = 0;
                }
            } else {
                $response['msg'] = "Debes introducir un email";
                $response['status'] = 0;
            }

        }catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
            $response['status'] = 0;
        }
        return response()->json($response);
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
                'biografia' => 'required',

            ]);

            if($validator->fails()){
                $response = ['status'=>0, 'msg'=>$validator->errors()->first()];
            } else {
                $user->nombre = $Data->nombre;
                $user->email = $Data->email;
                if(preg_match("/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{6,}/", $Data->password)){
                    $user->password = Hash::make($Data->password);
                }else{
                    $response['msg'] = " la contraseña no es segura";
                    return response()->json($response);
                }
                $user->puesto = $Data->puesto;
                $user->salario = $Data->salario;
                $user->biografia = $Data->biografia;
                $user->save();
                $response['msg'] = " el usuario ha sido creado correctamente";
                $response['status'] = 1;
            }

        }catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
            $response['status'] = 0;
        }
        return response()->json($response);
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
            $response['msg'] = "el usuario se ha creado correctamente";
            $response['status'] = 1;
        }catch (\Exception $error){
            $response['msg'] = "Ha ocurrido un error al añadir el usuario: ".$error->getMessage();
            $response['status'] = 0;
        }
        return response()->json($response);
    }

    public function listar (Request $req){

        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);


        $usuario = $req->usuario;


        if($usuario->puesto == 'directivo'){
            $empleados = User::where('puesto', 'rrhh')->orwhere('puesto', 'empleado')->get();
            $response['data'] = $empleados;
        }
        if($usuario->puesto == 'rrhh'){
            $empleados = User::where('puesto', 'empleado')->get();
            $response['data'] = $empleados;
        }


        return response()->json($response);

    }
    public function consultar (Request $req){

        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);

        $usuario = $req->usuario;
        $user_check = User::where('id',$Data->user_id)->first();

        if($user_check) {
            if($usuario->puesto == 'directivo'){
                if($user_check->role != 'directivo') {
                    $empleados = User::where('id', $Data->user_id)->get();
                    $empleados->makeVisible("biografia");
                    $response['data'] = $empleados;
                } else{
                    $response['msg'] = "No puedes consultar este usuario";
                    $response['status'] = 0;
                }
            } else if($usuario->puesto == 'rrhh'){
                if($user_check->role == 'empleado') {
                    $empleados = User::where('id', $Data->user_id)->get();
                    $empleados->makeVisible("biografia");
                    $response['data'] = $empleados;
                }else{
                    $response['msg'] = "No puedes consultar este usuario";
                    $response['status'] = 0;
                }
            }
        } else {
            $response['msg'] = "El usuario no existe";
            $response['status'] = 0;
        }


        return response()->json($response);

    }
    public function Perfil (Request $req){

        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);

        $usuario = $req->usuario;

        $usuario->makeVisible('email','biografia','api_token','created_at','updated_at');
        $response['data'] = $usuario;

        return response()->json($response);

    }

    public function editar (Request $req){

        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);

        $usuarioQueEdita = $req->usuario;
        $usuarioEditado = User::find($Data->user_id);

        switch ($usuarioQueEdita->puesto) {
            case 'RRHH':
                $rangoEditor = 2;
                break;
            case 'directivo':
                $rangoEditor = 3;
                break;
            default:
                $response['msg'] = "Puesto no identificado";
                break;
        }
        switch ($usuarioEditado->puesto) {
            case 'empleado':
                $rangoEditado = 1;
                break;
            case 'RRHH':
                $rangoEditado = 2;
                break;
            case 'directivo':
                $rangoEditado = 3;
                break;
            default:
                $response['msg'] = "Puesto no identificado";
                break;
        }

        if($rangoEditor > $rangoEditado || $usuarioQueEdita->id == $usuarioEditado->id) {
            if(isset($Data->nombre)) $usuarioEditado->nombre = $Data->nombre;
            if(isset($Data->email)) $usuarioEditado->email = $Data->email;
            if(isset($Data->password)) $usuarioEditado->password = Hash::make($Data->password);
            if(isset($Data->puesto)) $usuarioEditado->puesto = $Data->puesto;
            $usuarioEditado->save();
            $response['data'] = $usuarioEditado;
        }else{
            $response['msg'] = "No puedes modificar este usuario";
            $response['status'] = 0;
        }



        return response()->json($response);

    }

    public function RecuperarPass(Request $req){
        //recoger la info del request (viene del json)
        $JsonData = $req->getContent();
        //pasar el Json al objeto
        $Data = json_decode($JsonData);

        $user = User::where('email',$Data->email)->first();

        if($user){

            $NuevaPass = Str::random(20);
            $user->password = Hash::make($NuevaPass);
            $user->save();
            Mail::to($user->mail)->send(new PassRecovery($NuevaPass));
            $response['msg'] = "Contraseña cambiada correctamente: $NuevaPass";
            $response['status'] = 1;

        }else{
            $response['msg'] = "Ctm no se encuentra el email fds kkkkk";
            $response['status'] = 0;
        }

        return response()->json($response);






    }

}
