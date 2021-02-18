<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuarios;

class UsuarioController extends Controller
{
    //

    public function pruebas(Request $request){
        return "usuarios controller prueba";
    }

    public function registro(Request $request){

        $mensajeResHttp = new \MensajeHttp();
        $json= $request->input("json",null);

        $params = json_decode($json);
        $params_array= json_decode($json,true);

        if(empty($params) || empty($params_array)){
            $error= $mensajeResHttp->mensajeError(['mensaje'=>'Los datos enviados no son correctos']);
            return response()->json($error,$error['codigoEstado']);
        }

        try {
            $params_array = array_map('trim',$params_array);

            $validar = \Validator::make($params_array,[
                'nombre'=> 'required',
                'correo' => 'required|email|unique:tblUsuarios',
                'contrasena'=>'required'
            ]);

            if($validar->fails()){
                $error= $mensajeResHttp->mensajeError($validar->errors());
                return response()->json($error,$error['codigoEstado']);
            }

            $pwdCifrada= hash('sha256',$params->contrasena);

            $usuario= new Usuarios();

            $usuario->nombre = $params_array['nombre'];
            $usuario->correo = $params_array['correo'];
            $usuario->contrasena = $pwdCifrada;
            $usuario->idRol = 2;
            $usuario->save();

            $data= $mensajeResHttp->mensajeExito($usuario,'Se creo correctamente el usuario');
            return response()->json($data,$data['codigoEstado']);

        } catch (Exception $ex) {

           $error= $mensajeResHttp->mensajeError(array('mensaje'=>'Ocurrio un error interno'),$ex->getTrace() ,500);
            return response()->json($error,$error['codigoEstado']);

        }

    }

    public function inicioSesion(Request $request){
        $jwtAuth= new \JwtAuth();
        $mensajeResHttp = new \MensajeHttp();


        // $correo="angelfabriciogonzalez45@gmail.com";
        // $contrasena="1234566";
        // $passEncrypted= password_hash($contrasena,PASSWORD_BCRYPT,['cost'=>4]);


        try {

            $json= $request->input('json',null);
            $params= json_decode($json);
            $params_array= json_decode($json,true);

            if(empty($params) || empty($params_array)){
                $error= $mensajeResHttp->mensajeError(['mensaje'=>'Los datos enviados no son correctos']);
                return response()->json($error,$error['codigoEstado']);
            }
            $params_array = array_map('trim',$params_array);

            $validar = \Validator::make($params_array,[
                'correo' => 'required|email',
                'contrasena'=>'required'
            ]);

            if($validar->fails()){
                $error= $mensajeResHttp->mensajeError(array('mensaje'=>$validar->errors()));
                return response()->json($error,$error['codigoEstado']);
            }

            $passEncrypted= hash('sha256',$params->contrasena);

            if(!empty($params->obtenerToken)){

                $res= $jwtAuth->signup($params->correo,$passEncrypted,true);
            }else{

                $res= $jwtAuth->signup($params->correo,$passEncrypted);
            }


            if($res['existeError']){
               return response()->json($res,$res['codigoEstado']);
            }


            return response()->json($res,200);
        } catch (\Exception $e) {
            $error= $mensajeResHttp->mensajeError(array('mensaje'=>'Ocurrio un error interno'),$e->getMessage(),500);
            return response()->json($error,$error['codigoEstado']);
        }
    }


    public function actualizar(Request $request){
            $token = $request->header('Authorization');

            $jwt= new \JwtAuth();

            $verificarToken = $jwt->verificarToken($token);

            if($verificarToken){

            }

    }
}
