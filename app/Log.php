<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{   
    use \ Illuminate \ Database \ Eloquent \ SoftDeletes ;
    use \ Askedio \ SoftCascade \ Traits \ SoftCascadeTrait ;
    // Decirle a Eloquen   la llave primaria del modelo
    protected $primaryKey = 'id_log';
    // Asginar modelo a tabla
    protected $table = 'log';

    //Asginar elementos con carga masiva
    protected $fillable =['descripcion','fecha'];
    // elementos de Agreguemos el Trait SoftDeletes y la propiedad dates
    protected $dates = ['deleted_at'];
  
    //relacion inversa uno a muchos
    public function usuario(){
        return $this->belongsto('App\Usuario','id_usuario');
    }
}
