<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\MensajeRespuesta;
use App\Categorias;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['pruebas', 'listar']]);
    }

    //
    public function pruebas(Request $request) {
        return "categorai controller prueba desde heroku";
    }

    public function listar() {

        $mensajeResHttp = new \MensajeHttp();


        try {

            $categorias = Categorias::where('estado', 1)->get();

            $res = $mensajeResHttp->mensajeExitoV2($categorias, '');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Exception $ex) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'ocurrio un error interno'), $ex->getTrace(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function guardar(Request $request) {


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
                        'nombre' => 'required',
                        'descripcion' => 'required'
            ]);

            if ($validar->fails()) {
                $error = $mensajeResHttp->mensajeError($validar->errors());
                return response()->json($error, $error['codigoEstado']);
            }

            $jwtAuth = new \JwtAuth();
            $usuarioLogueado = $jwtAuth->usuarioToken($request);


            $categoria = new Categorias();
            $categoria->nombre = $params->nombre;
            $categoria->descripcion = $params->descripcion;
            $categoria->creadoPor = $usuarioLogueado->id;

            $categoria->save();

            $res = $mensajeResHttp->mensajeExito($categoria, 'se creo correctamente');
            return response()->json($res, $res["codigoEstado"]);
        } catch (\Exception $ex) {

            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'ocurrio un error interno'), $ex->getTrace(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function actualizar($id, Request $request) {

        $json = $request->input('json', null);
        $mensajeResHttp = new \MensajeHttp();



        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (empty($params_array) || empty($params)) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los datos enviados no son correctos'), '');
            return response()->json($error, $error['codigoEstado']);
        }


        try {
            array_map('trim', $params_array);

            $validar = \Validator::make($params_array, [
                        'nombre' => 'required',
                        'descripcion' => 'required',
                        'estado' => 'estado'
            ]);

            if ($validar->fails()) {
                $error = $mensajeResHttp->mensajeError($validar->errors(), '');
                return response()->json($error, $error['codigoEstado']);
            }

            unset($params_array->id);
            unset($params_array->created_at);
            unset($params_array->creadoPor);


           $categoriaExiste = Categorias::where('id',$id)->get();
           /*
           if(empty($categoriaExiste) || is_empty($categoriaExiste) ){
               $error = $mensajeResHttp->mensajeError(array('mensaje' => 'No se encontró categoria'), '');
                return response()->json($error, $error['codigoEstado']);
           }*/

             Categorias::where('id', $id)->update($params_array);
             $categoria= Categorias::where('id',$id)->get();

            $res = $mensajeResHttp->mensajeExito($categoria[0], 'Se actualzo correctamente.');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Exception $ex) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getTrace(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function eliminar($id) {

        $mensajeResHttp = new \MensajeHttp();

        if ( is_null($id) || empty($id) ) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los parametros enviados no son validos'), '');
            return response()->json($error, $error['codigoEstado']);
        }

        try {

            $categoria = Categorias::where('id', $id)->first();

            if (empty($categoria)) {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => 'No se encontró la categoria'), '');
                return response()->json($error, $error['codigoEstado']);
            }

            $categoria->delete();

            $res = $mensajeResHttp->mensajeExito($categoria, 'Se elimino correctamente.');
            return response()->json($res, $res['codigoEstado']);

        } catch (\Exception $ex) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getTrace(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

}
