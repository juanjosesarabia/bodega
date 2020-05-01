<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ingreso;
use App\Salida;

class ControladorSalida extends Controller
{
    //método para registrar salida
    public function registerSalidaNormal(Request $req){
        $salida =  new Salida; //instancia del modelo       
                
        $salida->cedulaNombreRecibe = $req->input('cedulaNombreRecibe');
        $salida->nombreRecibe = $req->input('nombreRecibe');
        $salida->fechasalida = $req->input('fechasalida');
        $salida->numero_acta = $req->input('numero_acta');  
        $salida->cantidadIngresada = $req->input('cantidadIngresada');  
        $salida->ubicacionOperativo = $req->input('ubicacionOperativo');  
        $salida->id_vendedor = $req->input('id_vendedor');              
        
        if(!$salida->save()){
            $data =["estado"=>"error","mensaje"=>"La salida se registro exitosamente"];    
            return response($data, 402); 
         }else{
            $salida->save();                      
            $data =["estado"=>"ok","mensaje"=>"La salida se registro exitosamente"];    
            return response($data, 200);  
         }                
     } //
}
