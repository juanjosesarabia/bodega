<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{   //Asginar modelo a tabla
    protected $table = 'producto';
    //Asginar  elementos a carga masiva
    protected $fillabe =['nombre','descripcion','codigoBarra', 'riesgo'];

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
