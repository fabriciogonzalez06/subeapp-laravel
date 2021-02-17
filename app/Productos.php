<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    //
    protected $table ='tblproductos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre', 'descripcion', 'estado','precio','imagen','creadoPor', 'created_at', 'updated_at'
    ];

    //relacion de 1 a muchos inversa (muchos a uno)
    //muchos productos a un susuario

    public function usuario(){
        return $this->belongsTo('App\Usuarios','creadoPor');
    }

    public function categoria(){
        return $this->belongsTo('App\Categorias','idCategoria');
    }


}
