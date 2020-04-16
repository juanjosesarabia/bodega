<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Usuario;
use Illuminate\Support\Facades\Validator;

class ControladorUsuario extends Controller
{
    //método para validar usuario registrado
    public function validateUser(Request $req){        
        $usuario =  new Usuario; //instancia del modelo
        $user = Usuario::get(); // se obtiene todos los objetos de la BD
                
        $correo= $req->input('correo'); //registros a comparar 
        $contrasena= $req->input('contrasena'); 

        if(!$correo||!$contrasena){
          $data =["estado"=>"error","mensaje"=>"Correo y contraseña son obligatorios"];            
          return response($data,404);
      }
        $cont=0;
        foreach($user as $fila) {         
           if ($fila->correo==$correo && $fila->contrasena==$contrasena) {
            $cont++;  //se verifica duplicidad                   
          }}       
        
          if ($cont==0) {
            $data =["estado"=>"error","mensaje"=>"Credenciales invalidas"];            
            return response($data,401);
          } else {
            
            $data =["estado"=>"ok","mensaje"=>"Acceso Concedido"];            
            return response($data,200);            
          }
    }
    
    //método para registrar usuario 
    public function registerUser(Request $req){
        $usuario =  new Usuario; //instancia del modelo
        $user = Usuario::withTrashed()->get(); // se obtiene todos los objetos de la BD
                
        $cedula= $req->input('cedula'); //registros a comparar 
        $correo= $req->input('correo'); 
        if(!$cedula||!$correo){
          $data =["estado"=>"error","mensaje"=>"Existen campos vacios que son obligatorios"];            
          return response($data,404);
      }
        $cont=0;
        foreach($user as $fila) {         
           if ($fila->cedula==$cedula||$fila->correo==$correo) {
            $cont++;  //se verifica duplicidad                   
          }}       
        
          if ($cont!=0) {
            $data =["estado"=>"error","mensaje"=>"Usuario ya posee credenciales,correo o cédula registrada"];            
            return response($data,400);
          } else {
            $usuario->cedula = $req->input('cedula');
            $usuario->nombres = $req->input('nombres');
            $usuario->apellidos = $req->input('apellidos');
            $usuario->correo = $req->input('correo');
            $usuario->contrasena = $req->input('contrasena'); 
            
            if(!$usuario->cedula ||!$usuario->nombres ||!$usuario->apellidos ||!$usuario->correo ||!$usuario->contrasena){
              $data =["estado"=>"error","mensaje"=>"Existen campos vacíos que son obligatorios"];            
              return response($data,404);
                } else{
                      $usuario->save();
                      $data =["estado"=>"ok","mensaje"=>"Usuario registrado con exito"];    
                      return response($data, 200); 
                    }          
          }      
     }
    
      //método para obtener todos los datos registrados
    public function userAll(){
      $user = Usuario::all();
      $datos =[];
       
      if($user->isEmpty()){//verificar si en la bd hay registros
        $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
        return response($data,404);        
      }else{
        foreach($user as $fila) { 
          $datos1 = array("id"=>$fila->id_usuario,"cedula"=>$fila->cedula,"nombres"=>$fila->nombres,"apellidos"=> $fila->apellidos,"correo"=> $fila->correo,"tipo_usuario"=>$fila->tipo_usuario);   
          array_push($datos, $datos1);                            
         }
        return response($datos, 200);        
        }
    }


    //método para editar usuario registrado
    public function editUser(Request $req){
      $id =  $req->input('id');     
           
      
      if(!Usuario::find($id)){//verificar si en la bd hay registros
            $data =["estado"=>"error","mensaje"=>"No se encontró dato de usuario a modificar"]; 
        return response($data,404);        
      }
      else{   
           $userAll =Usuario::withTrashed()->where("id_usuario","!=",$id)->get();
           
           $user = Usuario::find($id); 

        $cedula= $req->input('cedula'); //registros a comparar 
        $correo= $req->input('correo'); 
          if(!$cedula||!$correo){
            $data =["estado"=>"error","mensaje"=>"Existe un campo vacío que es obligatorio"];            
            return response($data,404);
            }
        $cont=0;

        foreach($userAll as $fila) {         
           if ($fila->cedula==$cedula||$fila->correo==$correo ) {
            $cont++;  //se verifica duplicidad           
          }}
           if($cont==0){

             
              $user->cedula = $req->input('cedula');
              $user->nombres = $req->input('nombres');
              $user->apellidos = $req->input('apellidos');
              $user->correo = $req->input('correo');
              $user->contrasena = $req->input('contrasena');  

              if(!$user->nombres ||!$user->apellidos  ||!$user->contrasena){
                $data =["estado"=>"error","mensaje"=>"Existen campos vacíos que son obligatorios"];            
                return response($data,404);
              }else{
                $user->save();
                $data =["estado"=>"ok","mensaje"=>"Usuario modificado con exito"];    
                return response($data, 200);
              }
              
            }else{
              $data =["estado"=>"error","mensaje"=>"Informacion de cédula o correo pertenece a otro usuario"];            
              return response($data,400);
             }
      } 
    }
     //método para elimiar usuario registrado
    public function deletetUser(Request $req){      

      $validator = Validator::make($req->all(), [
        'id' => 'required|numeric',        
    ]);

    if ($validator->fails()) {
        $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
        return response($data,404);                  
    }

      $id =  $req->input('id');      
      $user = Usuario::find($id);        
      if(!$user){
        $data =["estado"=>"error","mensaje"=>"Usuario  no se encuentra en registrado base de datos"];            
        return response($data,404);
      }else{
        $user->delete();
        $data =["estado"=>"ok","mensaje"=>"Usuario eliminado exitosamente"];            
        return response($data,200);
         
      }     
        
    }
    //metodo para obtener todos los usuarios registrados incluyendo los borrados
    public function userAllDelete(){
      $user = Usuario::onlyTrashed()->get();
      $datos =[];
       
      if($user->isEmpty()){//verificar si en la bd hay registros
        $data =["estado"=>"error","mensaje"=>"No hay datos eliminados guardados"]; 
        return response($data,404);        
      }else{
        foreach($user as $fila) { 
          $datos1 = array("id"=>$fila->id_usuario,"cedula"=>$fila->cedula,"nombres"=>$fila->nombres,"apellidos"=> $fila->apellidos,"correo"=> $fila->correo,"tipo_usuario"=>$fila->tipo_usuario,"Eliminado"=>$fila->deleted_at);   
          array_push($datos, $datos1);                            
         }
        return response($datos, 200);        
        }
    }

   
     
     //método para buscar usuario registrado
     public function searchUser($id){
      if(Usuario::find($id)){
          $user = Usuario::find($id);
          $datos1 = array("id"=>$user->id_usuario,"cedula"=>$user->cedula,"nombres"=>$user->nombres,"apellidos"=> $user->apellidos,"correo"=> $user->correo,"tipo_usuario"=>$user->tipo_usuario); 
          return response($datos1,200);
      }else{
        $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
        return response($data,404);  
      }

      


     }
      //método para resetear contraseña de usuario registrado
     public function resetPassword(Request $req){
      $validator = Validator::make($req->all(), [
        'id' => 'required|numeric',        
    ]);

    if ($validator->fails()) {
        $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
        return response($data,404);                  
    }
      $id =  $req->input('id');       
      
      if(Usuario::find($id)!=null){
        $user =  Usuario::find($id);
        $user->contrasena = $req->input("contrasenaNueva");
        if(!$user->contrasena){
          $data =["estado"=>"error","mensaje"=>"Contrasena nueva esta vacía"]; 
          return response($data,404); 
        }else{
          $user->save();
          $data =["estado"=>"ok","mensaje"=>"Contraseña modificado con exito"];    
          return response($data, 200);}
          

      }else{
        $data =["estado"=>"error","mensaje"=>"Usuario no se encuentra en base de datos"]; 
        return response($data,404); 
      }


    }
    //método para cambiar el tipo de usuario registrado
    public function cambiarTipo(Request $req){
          $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',        
        ]);

        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
            return response($data,404);                  
        }
          $id =  $req->input('id');
          
          if(Usuario::find($id)!=null){
            $user =  Usuario::find($id);        
            $validator = Validator::make($req->all(), [
              'tipoUsuario' => 'required|string',        
          ]);
          $user->tipo_usuario = $req->input("tipoUsuario");
          if ($validator->fails()) {
              $data =["estado"=>"error","mensaje"=>"Tipo de usuario  a guardar esta vacío o no es una cadena"];            
              return response($data,404);                  
          }else{
                $user->save();
              $data =["estado"=>"ok","mensaje"=>"Tipo de usuario modificado con exito"];    
              return response($data, 200);
            }
              

          }else{
            $data =["estado"=>"error","mensaje"=>"Usuario no se encuentra en base de datos"]; 
            return response($data,404); 
          }
    }

    //método restaurar usuario borrado
    public function restoreUser(Request $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric'       
      ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
          return response($data,404);                  
      }
        $id =  $req->input('id'); 
        $user =Usuario::onlyTrashed()->find($id); 
        
          if($user && $user->deleted_at !=null){//verifica que el usuario cumpla las condciones         
          Usuario::onlyTrashed()->find($id)->restore();
          $data =["estado"=>"ok","mensaje"=>"Usuario restaurado con exito"]; 
          return response($data,200);
        
        }else{
          $data =["estado"=>"error","mensaje"=>"No se restauro usuario,usuario no se encuentra eliminado"]; 
          return response($data,404);  
        }
    }

}
