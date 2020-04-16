<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendedor;

class ControladorVendedor extends Controller
{
    
    //método para registrar vendedor 
    public function registerVendedor(Request $req){
        $vendedor =  new Vendedor; //instancia del modelo
        $vend = Vendedor::withTrashed()->get(); // se obtiene todos los objetos de la BD
                
        $cedula= $req->input('cedula'); //registros a comparar         

        if(!$cedula){
            $data =["estado"=>"error","mensaje"=>"La cédula del vendedor esta vacía"];            
            return response($data,404);
        }
        $cont=0;
        foreach($vend as $fila) {         
           if ($fila->cedula==$cedula) {
            $cont++;  //se verifica duplicidad                   
          }}       
        
          if ($cont!=0) {
            $data =["estado"=>"error","mensaje"=>"Vendedor ya registrado"];            
            return response($data,400);
          } else {
              
            $vendedor->cedula = $req->input('cedula');
            $vendedor->nombres = $req->input('nombres');
            $vendedor->apellidos = $req->input('apellidos');
            $vendedor->telefono = $req->input('telefono'); 
            
            if(!$vendedor->telefono){
                $vendedor->telefono=0;
            }
             
            if(!$vendedor->nombres||!$vendedor->apellidos){
                $data =["estado"=>"error","mensaje"=>"No se guardo el vendedor, Nombres y apellidos son obligatorios"];            
                return response($data,404);                
            } else{
                $vendedor->save();
                $data =["estado"=>"ok","mensaje"=>"Vendedor registrado con exito"];    
                return response($data, 200);  
            }              
           
          }      
     }
    
    
    //
}
