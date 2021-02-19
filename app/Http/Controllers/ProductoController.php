<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProductoController extends Controller {

    //

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['listar']]);
    }

    public function pruebas(Request $request) {
        return "producto controller prueba";
    }

    public function listar(Request $request) {

        $json = $request->input('json', null);
        $mensajeResHttp = new \MensajeHttp();

        $params = json_decode($json);

        if (empty($params)) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los parametros enviados no son validos', 'para' => $params));
            return response()->json($error, $error["codigoEstado"]);
        }

        try {
            $spParams = array($params->estado, $params->id);
            $data = DB::select('call sp_tblProductos_listar(?, ?)', $spParams);

            $res = $mensajeResHttp->mensajeExitoV2($data, '');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Illuminate\Database\QueryException $ex) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getMessage(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function guardar(Request $request) {


        $todo = $request->all();

        /*

          return response()->json($todo, 200);


         */

        //$json = $request->input('json', null);
        // $json = $request->input('json', null);

        $mensajeResHttp = new \MensajeHttp();
        $jwtAuth = new \JwtAuth();

        //$imagen = $request->file('imgProducto');

        $imagen = $todo['imgProducto'];


        if (empty($imagen) || !$imagen) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'No se envio la imagen'), '');
            return response()->json($error, $error["codigoEstado"]);
        }
        /*
          try{
          //$urlImagen = \Cloudinary::upload($imagen->getRealPath())->getSecurePath();
          $urlImagen=  Cloudinary::upload($request->file('imgProducto')->getRealPath())->getSecurePath();
          } catch (\Exception $ex) {
          $urlImagen= null;
          $error = $mensajeResHttp->mensajeError(array('mensaje' => 'No se pudo subir la imagen'), $ex->getTraceAsString());
          return response()->json($error, $error["codigoEstado"]);
          } */

        // $params = json_decode($json);
        //$params_array = json_decode($json, true);
        $params = json_decode($todo['json']);
        $params_array = json_decode($todo['json'], true);





        try {

            if (empty($params) || empty($params_array)) {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los parametros enviados no son validos'), '');
                return response()->json($error, $error["codigoEstado"]);
            }

            array_map('trim', $params_array);

            $validar = \Validator::make($params_array, [
                        'nombre' => 'required',
                        'precio' => 'required',
                        'idCategoria' => 'required',
                        'descripcion'=>'required'
            ]);

            if ($validar->fails()) {
                $error = $mensajeResHttp->mensajeError($validar->errors(), '');
                return response()->json($error, $error["codigoEstado"]);
            }

            $usuarioLogueado = $jwtAuth->usuarioToken($request);

            $urlImagen = Cloudinary::upload($request->file('imgProducto')->getRealPath())->getSecurePath();
            /*
              $spParams = array(
              $params->nombre,
              $params->descripcion,
              $params->precio,
              $urlImagen,
              $params->idCategoria,
              $usuarioLogueado->id
              ); */

            $spParams = array(
                $params_array['nombre'],
                $params_array['descripcion'],
                $params_array['precio'],
                $urlImagen,
                $params_array['idCategoria'],
                $usuarioLogueado->id
            );

            $data = DB::select('call sp_tblProductos_nuevo(?, ?,?,?,?,?)', $spParams);

            $res = $mensajeResHttp->mensajeExitoV2($data, 'se creo correctamente');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Illuminate\Database\QueryException $ex) {
            //OPTIMIZAR
             $separado= explode("/", $urlImagen);
             $separado2= explode(".", $separado[count($separado)-1]);
            if ($ex->getCode() === "45000") {

                Cloudinary::uploadapi()->destroy($separado2[0]);
                $error = $mensajeResHttp->mensajeError(array('mensaje' => $ex->getPrevious()->errorInfo[2]), $ex->getPrevious(), 400);
                return response()->json($error, $error['codigoEstado']);
            }
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getMessage(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function actualizar($id, Request $request) {

        $json = $request->input('json', null);
        $mensajeResHttp = new \MensajeHttp();
        $jwtAuth = new \JwtAuth();

        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (empty($params) || empty($params_array) || empty($id)) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los parametros enviados no son validos'), '');
            return response()->json($error, $error["codigoEstado"]);
        }

        try {

            array_map('trim', $params_array);

            $opcionValidar = \Validator::make($params_array, [
                        'soloEstado' => 'required|bool'
            ]);


            if ($opcionValidar->fails()) {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Debe enviar la propiedad booleana soloEstado'), '');
                return response()->json($error, $error["codigoEstado"]);
            }


            $usuarioLogueado = $jwtAuth->usuarioToken($request);


            if ($params->soloEstado === 1) {

                //solo va a actualizar el estado nada mas

                $validar = \Validator::make($params_array, [
                            'estado' => 'required'
                ]);

                if ($validar->fails()) {
                    $error = $mensajeResHttp->mensajeError($validar->errors(), '');
                    return response()->json($error, $error["codigoEstado"]);
                }


                $spParams = array(
                    $id,
                    null,
                    null,
                    null,
                    null,
                    $params->estado,
                    $usuarioLogueado->id,
                    1
                );

                $data = DB::select('call sp_tblProductos_actualizar(?, ?,?,?,?,?,?,?)', $spParams);

                $res = $mensajeResHttp->mensajeExitoV2($data, 'se creo correctamente');
                return response()->json($res, $res['codigoEstado']);
            } else {

                //va a actualizar todo el objeto
                $validar = \Validator::make($params_array, [
                            'nombre' => 'required',
                            'precio' => 'required',
                            'idCategoria' => 'required',
                            'descripcion' => 'required'
                ]);

                if ($validar->fails()) {
                    $error = $mensajeResHttp->mensajeError($validar->errors(), '');
                    return response()->json($error, $error["codigoEstado"]);
                }

                $spParams = array(
                    $id,
                    $params->nombre,
                    $params->descripcion,
                    $params->precio,
                    $params->idCategoria,
                    null,
                    $usuarioLogueado->id,
                    0
                );

                $data = DB::select('call sp_tblProductos_actualizar(?, ?,?,?,?,?,?,?)', $spParams);

                $res = $mensajeResHttp->mensajeExitoV2($data, 'se creo correctamente');
                return response()->json($res, $res['codigoEstado']);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            //OPTIMIZAR
            if ($ex->getCode() === "45000") {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => $ex->getPrevious()->errorInfo[2]), $ex->getPrevious(), 400);
                return response()->json($error, $error['codigoEstado']);
            }
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getMessage(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function actualizarFoto($id, Request $request) {

        $mensajeResHttp = new \MensajeHttp();
        $jwtAuth = new \JwtAuth();



        if (empty($id) || empty($id)) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los parametros enviados no son validos'), '');
            return response()->json($error, $error["codigoEstado"]);
        }



        try {

            $usuarioLogueado = $jwtAuth->usuarioToken($request);
             $urlImagen = Cloudinary::upload($request->file('imgProducto')->getRealPath())->getSecurePath();

            $spParams = array(
                $id,
                $usuarioLogueado->id,
                $urlImagen
            );

            $data = DB::select('call sp_tblProductos_actualizarFoto(?, ?,?)', $spParams);

            $res = $mensajeResHttp->mensajeExitoV2($data, 'se actualizo correctamente');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Illuminate\Database\QueryException $ex) {
            //OPTIMIZAR
            if ($ex->getCode() === "45000") {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => $ex->getPrevious()->errorInfo[2]), $ex->getPrevious(), 400);
                return response()->json($error, $error['codigoEstado']);
            }
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getPrevious(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

    public function eliminar($id, Request $request) {

        $json = $request->input('json', null);
        $mensajeResHttp = new \MensajeHttp();
        $jwtAuth = new \JwtAuth();


        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (empty($params) || empty($params_array)) {
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Los parametros enviados no son validos'), '');
            return response()->json($error, $error["codigoEstado"]);
        }

        try {

            array_map('trim', $params_array);

            $validar = \Validator::make($params_array, [
                        'soloDesactivar' => 'required|bool'
            ]);

            if ($validar->fails()) {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Envie la propiedad booleana soloDesactivar'), '');
                return response()->json($error, $error["codigoEstado"]);
            }

            $usuarioLogueado = $jwtAuth->usuarioToken($request);


            $spParams = array(
                $id,
                $usuarioLogueado->id,
                $params->soloDesactivar
            );

            $data = DB::select('call sp_tblProductos_eliminar(?,?,?)', $spParams);

            $res = $mensajeResHttp->mensajeExitoV2($data, 'se elimino correctamente');
            return response()->json($res, $res['codigoEstado']);
        } catch (\Illuminate\Database\QueryException $ex) {
            //OPTIMIZAR
            if ($ex->getCode() === "45000") {
                $error = $mensajeResHttp->mensajeError(array('mensaje' => $ex->getPrevious()->errorInfo[2]), $ex->getPrevious(), 400);
                return response()->json($error, $error['codigoEstado']);
            }
            $error = $mensajeResHttp->mensajeError(array('mensaje' => 'Ocurrio un error interno'), $ex->getMessage(), 500);
            return response()->json($error, $error['codigoEstado']);
        }
    }

}
