<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{   //asignar tabla a  modelo
    protected $table = 'vendedor'; 
    //Asignar elementos a carga masiva
    protected $fillable =["cedula", "nombres", "apellidos","telefono", "historial"];

    //relacion 1 a muchos
    public function productos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Producto','id_vendedor', 'id_vendedor ');
    }
    //relacion 1 a muchos
    public function ingresos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Ingreso','id_vendedor', 'id_vendedor ');
    }
    //relacion 1 a muchos
    public function salidas(){// relacion - llave foranea - llave local
        return $this->hasMany('App\salida','id_vendedor', 'id_vendedor ');
    }
}
