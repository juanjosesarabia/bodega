<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{   //Asignar modelo a tabla
    protected $table = 'salida'; 
    //Asginar elementos a carga masiva
    protected $fillable =['cedulaNombreRetira','nombreRetira','salidaAprobada','fechaSalida','cedulaNombreOficiaSalida','nombreOficiaSalida',"cantidadRetirada"];
          
    
    //relacion inversa uno a muchos
    public function vendedor(){
        return $this->belongsto('App\Vendedor','id_vendedor');
    }

     //relacion 1 a muchos
     public function productos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Producto','id_salida', 'id_salida');
    }

}
