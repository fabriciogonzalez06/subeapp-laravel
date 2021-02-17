<?php


namespace App\Helpers;

class MensajeRespuesta{


    public function  mensajeExito($datos,$mensaje,$codigoEstado=200){
        return array(
            'existeError'=>false,
            'errores'=>null,
            'codigoEstado'=>$codigoEstado,
            'datos'=> [$datos],
            'mensaje'=>$mensaje
        );
    }
    
    //para selects 
     public function  mensajeExitoV2($datos,$mensaje,$codigoEstado=200){
         
       //  $type= gettype($datos);
        return array(
            'existeError'=>false,
            'errores'=>null,
            'codigoEstado'=>$codigoEstado,
            'datos'=> $datos,
            'mensaje'=>$mensaje
        );
    }

    public function  mensajeError($errores,$errorInterno=null,$codigoEstado=400){
        return array(
            'existeError'=>true,
            'errores'=>[$errores],
            'codigoEstado'=>$codigoEstado,
            'datos'=> null,
            'errorInterno'=> $errorInterno,

        );
    }

    public function  mensajeErrorValidaciones($errores,$errorInterno=null,$codigoEstado=400){
        return array(
            'existeError'=>true,
            'errores'=>$errores,
            'codigoEstado'=>$codigoEstado,
            'datos'=> null,
            'errorInterno'=> $errorInterno
        );
    }

    public function darFormatoErroresValidaciones($errores){

            $formatoCorrecto=[];

            foreach($errores as $clave){
                    return ["hola"];
                    array_push($formatoCorrecto,"error");

            }

            // array_push($formatoCorrecto,array("error"=>$errores));



            return  get_object_vars( $errores );
    }

}
