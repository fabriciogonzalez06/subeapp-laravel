<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;


use App\Usuarios;

class JwtAuth{

    public $key;

    public function __constructor(){
        $this->key ="lñadsfjklkjlasdfkasdf****///*_______publickey";
    }

    public function signup($correo, $contrasena,$obtenerToken=null){

        $mensajeResHttp = new \MensajeHttp();

        $usuario= Usuarios::where([
            'correo'=>$correo,
            'contrasena'=>$contrasena
         ])->first();

         $usuarioCorrecto=false;

         if(is_object($usuario)){
             $usuarioCorrecto=true;
         }

         if(!$usuarioCorrecto)return $mensajeResHttp->mensajeError(array('mensaje'=>'Credenciales incorrectas!'));


         $token= array(
            'id' => $usuario->id,
            'correo'=> $usuario->correo,
            'nombre' => $usuario->nombre,
            'idRol' => $usuario->idRol,
            'iat'   => time(),
            'exp'    => time() + ( 24 * 60 * 60)
        );


        //  $jwt= JWT::encode($token , $this->key,'HS256');
        //  $decoded = JWT::decode($jwt, $this->key ,['HS256']);
         $jwt= JWT::encode($token ,"lñadsfjklkjlasdfkasdf****///*_______publickey",'HS256');
         $decoded = JWT::decode($jwt, "lñadsfjklkjlasdfkasdf****///*_______publickey" ,['HS256']);

         if(is_null($obtenerToken)){
                return array(
                    'token'=>$jwt,
                    'existeError'=>false
                );
         }else{
            return array(
                'datos'=>$decoded,
                'existeError'=>false
            );
         }

    }

    public function verificarToken($jwt,$obtenerIdentidad=false){
            $auth= false;

            try {
                $decoded= JWT::decode($jwt,"lñadsfjklkjlasdfkasdf****///*_______publickey",['HS256']);
            } catch (\Exception $e) {
                //throw $th;
                $auth=false;

            }

            if(!empty($decoded) && is_object($decoded) && isset($decoded->id)){
                $auth=true;
            }

            if($obtenerIdentidad){
                return $decoded;
            }

            return $auth;
    }
    
    public function usuarioToken($request){
        
        $token = $request->header('Authorization');
        $usuario = $this->verificarToken($token,true);
        return $usuario;
        

        
    }

}



