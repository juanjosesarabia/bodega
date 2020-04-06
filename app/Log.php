<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{   // Asginar modelo a tabla
    protected $table = 'log';

    //Asginar elementos con carga masiva
    protected $fillable =['descripcion','fecha'];
  
    //relacion inversa uno a muchos
    public function usuario(){
        return $this->belongsto('App\Usuario','id_usuario');
    }
}
