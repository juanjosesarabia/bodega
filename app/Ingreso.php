<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{   //asignar el modelo a la tabla ingreso
    protected $table = 'ingreso'; //
   // asignar elementos con carga masiva.
    protected $fillable =['cedulaNombreRecibe','nombreRecibe','fechaIngreso',"numero_acta","cantidadIngresada",'ubicacionOperativo'];


     //relacion inversa uno a muchos
     public function vendedor(){
        return $this->belongsto('App\Vendedor','id_vendedor');
    }

     //relacion 1 a muchos
     public function productos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Producto','id_ingreso', 'id_ingreso ');
    }
}
