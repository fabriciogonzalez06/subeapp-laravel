<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MensajesFrmContacto;

class MensajeFrmContactoController extends Controller
{
    //
    
    public function __construct() {
        $this->middleware('api.auth',['except'=>['store']]);
     
    }
    
    public function pruebas(Request $request){
        return "mensaje frm contacto controller prueba";
    }
    
     public function index() {

        $mensajeResHttp = new \MensajeHttp();


        try {

            $mensajes = MensajesFrmContacto::all();

            $res = $mensajeResHttp->mensajeExitoV2($mensajes, '');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Exception $ex) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'ocurrio un error interno'), $ex->getTrace(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function store(Request $request) {


        $mensajeResHttp = new \MensajeHttp();

        $json = $request->input('json', null);

        if (!$json) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los dataos enviados no son correctos'));
            return request()->json($error, $error['codigoEstado']);
        }



        try {
            $params = json_decode($json);
            $params_array = json_decode($json, true);

            array_map('trim', $params_array);

            $validar = \Validator::make($params_array, [
                        'correo' => 'required|email',
                        'nombre' => 'required',
                         'mensaje'=>'required'
            ]);

            if ($validar->fails()) {
                $error = $mensajeResHttp->mensajeError($validar->errors());
                return response()->json($error, $error['codigoEstado']);
            }

        

            $mensaje = new MensajesFrmContacto();
            $mensaje->correo = $params->correo;
            $mensaje->mensaje = $params->mensaje;
            $mensaje->nombre = $params->nombre;

            $mensaje->save();

            $res = $mensajeResHttp->mensajeExito($mensaje, 'se creo correctamente');
            return response()->json($res, $res["codigoEstado"]);
        } catch (\Exception $ex) {

            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'ocurrio un error interno'), $ex->getTrace(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

}
