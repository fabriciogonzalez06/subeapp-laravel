<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    //
    protected $table ='tblUsuarios';

    //Relacion uno a muchos
    public function productos(){
        return $this->hasMany('App\Productos');
    }
}
