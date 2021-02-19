<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    //
    protected $table ='tblCategorias';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre', 'descripcion', 'estado','estado', 'creadoPor', 'created_at', 'updated_at'
    ];

    //Relacion uno a muchos
    public function productos(){
        return $this->hasMany('App\Productos');
    }
}
