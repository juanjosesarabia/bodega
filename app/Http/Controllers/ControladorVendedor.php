<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ControladorVendedorRequest;
use App\Vendedor;
use App\Log;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Validator;

class ControladorVendedor extends Controller
{
    
    //método para registrar vendedor 
    public function registerVendedor(ControladorVendedorRequest $req){
        $vendedor =  new Vendedor; //instancia del modelo
        $vend = Vendedor::withTrashed()->get(); // se obtiene todos los objetos de la BD
                
        $cedula= $req->input('cedula'); //registros a comparar 
        
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
                    $vendedor->save();
                     //Log Editar vendedor
                    $log =  new Log; 
                    $usuario = Auth::user();  
                    $log->descripcion= "Vendedor : ".$vendedor->cedula." ".$vendedor->nombres." registrado por : ".$usuario->name; 
                    $log->id_usuario= $usuario ->id ; 
                    $log->save(); 
                    $data =["estado"=>"ok","mensaje"=>"Vendedor registrado con exito"];    
                    return response($data, 200);  
            }              
           
     }   

    //método para obtener todos los datos registrados de vendedores
    public function vendedoresAll(){
        $ven = Vendedor::all();
        $datos =[];
         
        if($ven->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{
          foreach($ven as $fila) { 
            $datos1 = array("id"=>$fila->id_vendedor,"cedula"=>$fila->cedula,"nombres"=>$fila->nombres,"apellidos"=> $fila->apellidos,"telefono"=> $fila->telefono);   
            array_push($datos, $datos1);                            
           }
           

          return response($datos, 200);        
          }
      }
     
     //método para editar vendedor registrado
     public function editVendedor(ControladorVendedorRequest $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric',        
         ]);
  
        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
            return response($data,404);                  
         }
        $id =  $req->input('id');               
        
        if(!Vendedor::find($id)){//verificar si en la bd hay registros
              $data =["estado"=>"error","mensaje"=>"No se encontró dato de vendedor a modificar"]; 
              return response($data,404);        
        }else{   
             $userAll =Vendedor::withTrashed()->where("id_vendedor","!=",$id)->get();
             
             $user = Vendedor::find($id); 
             $cedula= $req->input('cedula'); //registros a comparar                    
             $cont=0;
  
              foreach($userAll as $fila) {         
              if ($fila->cedula==$cedula ) {
                $cont++;  //se verifica duplicidad           
              }}
             if($cont==0){             
                $user->cedula = $req->input('cedula');
                $user->nombres = $req->input('nombres');
                $user->apellidos = $req->input('apellidos');
                $user->telefono = $req->input('telefono');                         
                $user->save();
                 //Log Editar vendedor
                 $log =  new Log; 
                 $usuario = Auth::user();  
                 $log->descripcion= "Vendedor : ".$user->cedula." ".$user->nombres." ".$user->apellidos." editado por : ".$usuario->name; 
                 $log->id_usuario= $usuario ->id ; 
                 $log->save(); 
                $data =["estado"=>"ok","mensaje"=>"Vendedor modificado con exito"];    
                return response($data, 200);   
              }else{
                $data =["estado"=>"error","mensaje"=>"Informacion de cédula  pertenece a otro vendedor"];            
                return response($data,400);
               }
        } 
    }
   //método para elimiar vendedor registrado
   public function deleteVendedor(Request $req){      

        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',        
        ]);

        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
            return response($data,404);                  
        }

            $id =  $req->input('id');      
            $user = Vendedor::find($id);        
            if(!$user){
            $data =["estado"=>"error","mensaje"=>"Vendedor  no se encuentra en registrado base de datos"];            
            return response($data,404);
            }else{
            $user->delete();
            //Log Eliminar vendedor
            $log =  new Log; 
            $usuario = Auth::user();  
            $log->descripcion= "Vendedor : ".$user->cedula." ".$user->nombres." ".$user->apellidos." eliminado por : ".$usuario->name; 
            $log->id_usuario= $usuario ->id ; 
            $log->save(); 
            $data =["estado"=>"ok","mensaje"=>"Vendedor eliminado exitosamente"];            
            return response($data,200);
      
    }     
  }
    
  //metodo para obtener todos los vendedores registrados incluyendo los borrados
  public function vendedorAllDelete(){
    $user = Vendedor::onlyTrashed()->get();
    $datos =[];
     
    if($user->isEmpty()){//verificar si en la bd hay registros
      $data =["estado"=>"error","mensaje"=>"No hay datos eliminados guardados"]; 
      return response($data,404);        
    }else{
      foreach($user as $fila) { 
        $datos1 = array("id"=>$fila->id_vendedor,"cedula"=>$fila->cedula,"nombres"=>$fila->nombres,"apellidos"=> $fila->apellidos,"telefono"=> $fila->telefono,"Eliminado"=> $fila->deleted_at);   
        array_push($datos, $datos1);                            
       }
      return response($datos, 200);        
      }
  }


    //método para buscar vendedor registrado
    public function searchVendedor($id){
        if(Vendedor::find($id)){
            $user = Vendedor::find($id);
            $datos1 = array("id"=>$user->id_vendedor,"cedula"=>$user->cedula,"nombres"=>$user->nombres,"apellidos"=> $user->apellidos,"teléfono"=> $user->telefono);
            return response($datos1,200);
        }else{
          $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
          return response($data,404);  
        }}

    //método para buscar vendedor registrado
    public function searchVendedorCc($cc){
      $user = Vendedor::where('cedula',"=", $cc)->get(); 
      if(!$user->isEmpty()){
        
          foreach ($user as $fila) {
          $datos1 = array("id"=>$fila->id_vendedor,"cedula"=>$fila->cedula,"nombres"=>$fila->nombres,"apellidos"=> $fila->apellidos,"teléfono"=> $fila->telefono);
        }
        return response($datos1,200);
      }else{
        $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
        return response($data,404);       
      } 
      }
  
        //método restaurar vendedor borrado
    public function restoreVendedor(Request $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric'       
      ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
          return response($data,404);                  
      }
        $id =  $req->input('id'); 
        $user =Vendedor::onlyTrashed()->find($id); 
        
          if($user && $user->deleted_at !=null){//verifica que el usuario cumpla las condciones         
          Vendedor::onlyTrashed()->find($id)->restore();

          //Log Restaurar vendedor
          $log =  new Log; 
          $usuario = Auth::user();  
          $log->descripcion= "Vendedor : ".$user->cedula." ".$user->nombres." ".$user->apellidos." restaurado por : ".$usuario->name; 
          $log->id_usuario= $usuario ->id ; 
          $log->save(); 
          
          $data =["estado"=>"ok","mensaje"=>"Vendedor restaurado con exito"]; 
          return response($data,200);
        
        }else{
          $data =["estado"=>"error","mensaje"=>"No se restauro el vendedor,este no se encuentra eliminado"]; 
          return response($data,404);  
        }
    }
 }
    
    
    //

