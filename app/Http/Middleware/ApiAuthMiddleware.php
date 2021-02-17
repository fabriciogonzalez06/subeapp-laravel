<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $jwtAuth= new \JwtAuth();
        $mensajeResHttp = new \MensajeHttp();

        $token = $request->header('Authorization');

        if(empty($token)){
            $error= $mensajeResHttp->mensajeError(array('mensaje'=>'Token no válido.'),null,401);
            return response()->json($error,$error['codigoEstado']);
        }

        $ok = $jwtAuth->verificarToken($token);

        if($ok){
            return $next($request);
        }else{
            $error= $mensajeResHttp->mensajeError(array('mensaje'=>'Su sesión ha finalizado, ingrese nuevamente.'),null,401);
            return response()->json($error,$error['codigoEstado']);
        }

    }
}
