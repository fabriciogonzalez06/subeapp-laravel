<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Productos;
use App\Categorias;
use App\Usuarios;

class DemoController extends Controller
{
    //

    public function testOrm(){

        $productos= Productos::all();
        foreach($productos as $producto){
            echo  "<h1>".$producto->nombre."</h1>" ;
            echo "<p>".($producto->categoria->nombre)."</p>";
            echo "<p>".($producto->usuario->correo)."</p>";
        }

        die();
    }
}
