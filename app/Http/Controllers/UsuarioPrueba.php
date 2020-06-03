<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\User; 
use App\Log;
use App\Http\Requests\ControladorUsuarioRequest;
use Illuminate\Support\Facades\Auth; 
use Validator;
class UsuarioPrueba extends Controller
{
    public $successStatus = 200;
    

    public function validateUser(Request $req){ 

        $correo= $req->input('email'); //registros a comparar 
        $contrasena= $req->input('password'); 

        if(!$correo||!$contrasena){
          $data =["estado"=>"error","mensaje"=>"Correo y contraseña son obligatorios"];            
          return response($data,404);
      }
        if(Auth::attempt(['email' => $correo, 'password' => $contrasena])){ 
            $user = Auth::user(); 
            if($user->tipo_usuario=="No asignado"){
                $data =["estado"=>"error","mensaje"=>"No tienes aprobación de usuario Administrador"];            
                return response($data,401);
            }else{
              $datos["name"] =$user->name;
              $datos["tipo"] =$user->tipo_usuario;
              $success['token'] =  $user->createToken('SISCONI')-> accessToken; 
              $data =["estado"=>"ok","mensaje"=>"Acceso Concedido",'datos'=>$datos,'success' => $success]; 
              //Ingreso en sistema
              $log =  new Log;   
              $log->descripcion= "Ingreso al sistema usuario : ".$user->name ; 
              $log->id_usuario= $user->id ; 
              $log->save();                  
              return response($data,200); 
             }
        }else{ 
            $data =["estado"=>"error","mensaje"=>"Credenciales invalidas"];            
            return response($data,401);       
        } 
    }
    
    
    public function registerUser(ControladorUsuarioRequest $req){ 

        $usuario =  new User; //instancia del modelo
        $user = User::withTrashed()->get(); // se obtiene todos los objetos de la BD
                
        $cedula= $req->input('cedula'); //registros a comparar 
        $email= $req->input('email');         
        $cont=0;
        foreach($user as $fila) {         
           if ($fila->cedula==$cedula||$fila->email==$email) {
            $cont++;  //se verifica duplicidad                   
          }}       
        
          if ($cont!=0) {
            $data =["estado"=>"error","mensaje"=>"Usuario ya posee credenciales,correo o cédula registrada"];            
            return response($data,400);
          } else {
              $usuario->cedula = $req->input('cedula');
              $usuario->name = $req->input('name');             
              $usuario->email = $req->input('email');
              $usuario->password =  bcrypt($req->input('password')); 
              
              $usuario->save();
              $data =["estado"=>"ok","mensaje"=>"Usuario registrado con exito"];    
              return response($data, 200); 
            }  
 
    }
    
      //método para obtener todos los datos registrados
      public function userAll(){
        $user = User::all();
        $datos =[];
         
        if($user->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{
          foreach($user as $fila) { 
            $datos1 = array("id"=>$fila->id,"cedula"=>$fila->cedula,"nombres"=>$fila->name,"correo"=> $fila->email,"tipo_usuario"=>$fila->tipo_usuario);   
            array_push($datos, $datos1);                            
           }
          return response($datos, 200);        
        }}

     //método para editar usuario registrado
    public function editUser(ControladorUsuarioRequest $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric',        
         ]);
  
        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
            return response($data,404);                  
         }
        $id =  $req->input('id');               
        
        if(!User::find($id)){//verificar si en la bd hay registros
              $data =["estado"=>"error","mensaje"=>"No se encontró dato de usuario a modificar"]; 
              return response($data,404);        
        }else{   
             $userAll =User::withTrashed()->where("id","!=",$id)->get();
             
             $user = User::find($id); 
             $cedula= $req->input('cedula'); //registros a comparar 
             $email= $req->input('email');           
             $cont=0;
  
              foreach($userAll as $fila) {         
              if ($fila->cedula==$cedula||$fila->email==$email ) {
                $cont++;  //se verifica duplicidad           
              }}
             if($cont==0){             
                $user->cedula = $req->input('cedula');
                $user->name = $req->input('name');               
                $user->email = $req->input('email');
                $user->password =  bcrypt($req->input('password'));             
                $user->save();
                //Log Editar usuario
                $log =  new Log; 
                $usuario = Auth::user();  
                $log->descripcion= "Usuario : ".$user->cedula." ".$user->name." editado por : ".$usuario->name; 
                $log->id_usuario= $usuario ->id ; 
                $log->save();  
                $data =["estado"=>"ok","mensaje"=>"Usuario modificado con exito"];    
                return response($data, 200);   
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
        $user = User::find($id);        
        if(!$user){
          $data =["estado"=>"error","mensaje"=>"Usuario  no se encuentra  registrado base de datos"];            
          return response($data,404);
        }else{
          //Log Eliminar usuario
          $log =  new Log; 
          $usuario = Auth::user();  
          $log->descripcion= "Usuario : ".$user->cedula."  ".$user->name ." eliminado por: ".$usuario->name;  
          $log->id_usuario= $usuario->id ; 
          $log->save(); 
          $user->delete();
          $data =["estado"=>"ok","mensaje"=>"Usuario eliminado exitosamente"];            
          return response($data,200);
          
        }
      }

      //metodo para obtener todos los usuarios registrados incluyendo los borrados
    public function userAllDelete(){
        $user = User::onlyTrashed()->get();
        $datos =[];
         
        if($user->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos eliminados guardados"]; 
          return response($data,404);        
        }else{
          foreach($user as $fila) { 
            $datos1 = array("id"=>$fila->id,"cedula"=>$fila->cedula,"name"=>$fila->name,"email"=> $fila->email,"tipo_usuario"=>$fila->tipo_usuario,"Eliminado"=>$fila->deleted_at);   
            array_push($datos, $datos1);                            
           }
          return response($datos, 200);        
          }
      }


     //método para buscar usuario registrado
     public function searchUser($id){
      if(User::find($id)){
          $user = User::find($id);
          $datos1 = array("id"=>$user->id,"cedula"=>$user->cedula,"name"=>$user->name,"email"=> $user->email,"tipo_usuario"=>$user->tipo_usuario,"Eliminado"=>$user->deleted_at); 
          return response($datos1,200);
      }else{
        $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
        return response($data,404);  
      }
    }  

     //método para buscar usuario registrado
     public function searchUserCc($cc){
      $user = User::where('cedula',"=", $cc)->get();       
      if(!$user->isEmpty()){   
        
        foreach($user as $fila){
          $datos1 = array("id"=>$fila->id,"cedula"=>$fila->cedula,"name"=>$fila->name,"email"=> $fila->email,"tipo_usuario"=>$fila->tipo_usuario); 
        }
          return response($datos1,200);
      }else{
        $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
        return response($data,404);  
      } 
     }

      //método para resetear contraseña de usuario registrado
  public function resetPasswordAd(Request $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric',        
      ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
          return response($data,404);                  
      }
        $id =  $req->input('id');       
        
        if(User::find($id)!=null){
          $user =  User::find($id);
          $user->password = bcrypt($req->input("contrasenaNueva"));
          if(!$user->password){
            $data =["estado"=>"error","mensaje"=>"Contrasena nueva esta vacía"]; 
            return response($data,404); 
          }else{
            //Log cambiar tipo de usuario
            $log =  new Log; 
            $usuario = Auth::user();  
            $log->descripcion= " Cambio de contraseña de usuario: ".$user->name." realizado por ".$usuario->name; 
            $log->id_usuario= $usuario->id ; 
            $log->save();
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
        
      if(User::find($id)!=null){
            $user =  User::find($id);        
            $validator = Validator::make($req->all(), [
              'tipoUsuario' => 'required|string',        
          ]);
          $user->tipo_usuario = $req->input("tipoUsuario");
          if ($validator->fails()) {
              $data =["estado"=>"error","mensaje"=>"Tipo de usuario  a guardar esta vacío o no es una cadena"];            
              return response($data,404);                  
          }else{
            //Log cambiar tipo de usuario
              $log =  new Log; 
              $usuario = Auth::user();  
              $log->descripcion= " Usuario :".$user->cedula." : ".$user->name." fue cambiado su tipo por: ".$usuario->name; 
              $log->id_usuario= $usuario->id ; 
              $log->save(); 
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
        $user =User::onlyTrashed()->find($id); 
        
        if($user && $user->deleted_at !=null){//verifica que el usuario cumpla las condciones         
          User::onlyTrashed()->find($id)->restore();
          //Log Restaurar usuario
          $log =  new Log; 
          $usuario = Auth::user();  
          $log->descripcion= "Usuario : ".$user->cedula." ".$user->name ." restaurado por: ".$usuario->name; 
          $log->id_usuario= $usuario->id ; 
          $log->save(); 
          $data =["estado"=>"ok","mensaje"=>"Usuario restaurado con exito"]; 
          return response($data,200);
        
        }else{
          $data =["estado"=>"error","mensaje"=>"No se restauro usuario,usuario no se encuentra eliminado"]; 
          return response($data,404);  
        }
  }


}
