<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{   
    use \ Illuminate \ Database \ Eloquent \ SoftDeletes ;
    use \ Askedio \ SoftCascade \ Traits \ SoftCascadeTrait ;
    // Decirle a Eloquen   la llave primaria del modelo
    protected $primaryKey = 'id_producto';
    //Asginar modelo a tabla
    protected $table = 'producto';
    //Asginar  elementos a carga masiva
    protected $fillabe =['nombre','descripcion','codigoBarra', 'riesgo'];
    // elementos de Agreguemos el Trait SoftDeletes y la propiedad dates
    protected $dates = ['deleted_at'];

    //relacion inversa uno a muchos
    public function vendedor(){
        return $this->belongsto('App\Vendedor','id_vendedor');
    }

    //relacion inversa uno a muchos
    public function ingreso(){
        return $this->belongsto('App\Ingreso','id_ingreso');
    }
    //relacion inversa uno a muchos
    public function salida(){
        return $this->belongsto('App\Salida','id_salida');
    }
   
   
}
