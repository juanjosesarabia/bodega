<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingreso extends Model
{   //importar para usar softdeletes
    use \ Illuminate \ Database \ Eloquent \ SoftDeletes ;
    use \ Askedio \ SoftCascade \ Traits \ SoftCascadeTrait ;
    //asignar el modelo a la tabla ingreso
    // Decirle a Eloquen   la llave primaria del modelo
    protected $primaryKey = 'id_ingreso';
    protected $table = 'ingreso'; //
   // asignar elementos con carga masiva.
    protected $fillable =['cedulaNombreRecibe','nombreRecibe','fechaIngreso',"numero_acta","cantidadIngresada",'ubicacionOperativo'];
   // elementos de Agreguemos el Trait SoftDeletes y la propiedad dates
    protected $dates = ['deleted_at'];
   
    protected $softCascade = ['productos']; //indica la relación vendedor()
    

     //relacion inversa uno a muchos
     public function vendedor(){
        return $this->belongsto('App\Vendedor','id_vendedor');
    }

     //relacion 1 a muchos
     public function productos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Producto','id_ingreso', 'id_ingreso ');
    }

    public function salidas(){
        return $this->hasMany('App\Salida','id_ingreso', 'id_ingreso');
    }
}
