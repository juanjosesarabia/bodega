<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{   
    use \ Illuminate \ Database \ Eloquent \ SoftDeletes ;
    use \ Askedio \ SoftCascade \ Traits \ SoftCascadeTrait ;

    // Decirle a Eloquen   la llave primaria del modelo
    protected $primaryKey = 'id_salida';
    //Asignar modelo a tabla
    protected $table = 'salida'; 
    //Asginar elementos a carga masiva
    protected $fillable =['cedulaNombreRetira','nombreRetira','salidaAprobada','fechaSalida','cedulaNombreOficiaSalida','nombreOficiaSalida',"cantidadRetirada"];
    // elementos de Agreguemos el Trait SoftDeletes y la propiedad dates
    protected $dates = ['deleted_at'];   
    //indica la relación productos
    protected $softCascade = ['productos']; 
    
    //relacion inversa uno a muchos
    public function vendedor(){
        return $this->belongsto('App\Vendedor','id_vendedor');
    }

     //relacion 1 a muchos
     public function productos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Producto','id_salida', 'id_salida');
    }

}
