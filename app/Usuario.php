<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{    //asginar tabla usuario al modelo
    protected $table = 'usuario'; 
    // asignar  elementos con carga masiva
    protected $fillable=['cedula','nombres','apellidos','correo','contrasena'];
    // Elementos de modelo ocultos
    protected $hidden =['contrasena','rememberToken'];
        
    
    public function Logs(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Log','id_usuario', 'id_usuario ');
    }
}
