<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{   
    use \ Illuminate \ Database \ Eloquent \ SoftDeletes ;
    use \ Askedio \ SoftCascade \ Traits \ SoftCascadeTrait ;

    // Decirle a Eloquen   la llave primaria del modelo
    protected $primaryKey = 'id_vendedor';
    
    //asignar tabla a  modelo
    protected $table = 'vendedor'; 
    //Asignar elementos a carga masiva
    protected $fillable =["cedula", "nombres", "apellidos","telefono", "historial"];
    // elementos de Agreguemos el Trait SoftDeletes y la propiedad dates
    protected $dates = ['deleted_at'];

     //indica la relación productos(), ingresos(), salidas()
     protected $softCascade = ['productos','ingresos'];
      
    

    //relacion 1 a muchos
    public function productos(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Producto','id_vendedor', 'id_vendedor ');
    }
    
    public function ingresos(){
        return $this->hasMany('App\Ingreso','id_vendedor', 'id_vendedor ');
    }
    
    
}
