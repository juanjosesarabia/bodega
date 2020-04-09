<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{   
    use \ Illuminate \ Database \ Eloquent \ SoftDeletes ;
    use \ Askedio \ SoftCascade \ Traits \ SoftCascadeTrait ;
    // Decirle a Eloquen   la llave primaria del modelo
    protected $primaryKey = 'id_usuario';       
    
    //asginar tabla usuario al modelo
    protected $table = 'usuario'; 
    // asignar  elementos con carga masiva
    protected $fillable=['cedula','nombres','apellidos','correo','contrasena'];
    // Elementos de modelo ocultos
    protected $hidden =['contrasena','rememberToken'];
   // elementos de Agreguemos el Trait SoftDeletes y la propiedad dates
    protected $dates = ['deleted_at'];
   //indica la relación Logs ()
    protected $softCascade = ['Logs']; 
        
    
    public function Logs(){// relacion - llave foranea - llave local
        return $this->hasMany('App\Log','id_usuario', 'id_usuario ');
    }
}
