<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MensajesFrmContacto extends Model
{
    //
    protected  $table= 'tblmensajesfrmcontacto';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre', 'mensaje', 'estado','correo', 'created_at', 'updated_at'
    ];
}
