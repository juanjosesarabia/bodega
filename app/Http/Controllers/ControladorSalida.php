<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ingreso;
use App\Salida;
use App\Log;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ControladorSalida extends Controller
{
    //método para registrar salida
    public function registerSalidaNormal(Request $req){
        $salida =  new Salida; //instancia del modelo       
                
        $salida->cedulaNombreRetira = $req->input('cedulaNombreRetira');
        $salida->nombreRetira = $req->input('nombreRetira');
        $salida->fechaSalida = $req->input('fechaSalida');      
        $salida->id_ingreso = $req->input('id_ingreso');   
        
        
          $retiro = DB::table('producto')->where("id_ingreso","=", $salida->id_ingreso)->where("deleted_at","=",null )->where("riesgo","=",'no')->sum('cantidadUnitaria');
          $riesgo = DB::table('producto')->where("id_ingreso","=", $salida->id_ingreso)->where("deleted_at","=",null )->where("riesgo","!=",'no')->sum('cantidadUnitaria');
          
          $sal = Salida::withTrashed()->get(); // se obtiene todos los objetos de la BD       
          
          $cont=0;
          foreach($sal as $fila) {         
             if ($fila->datoSalida=="entrega" && $fila->id_ingreso==$salida->id_ingreso && $fila->deleted_at==null) {
              $cont++;  //se verifica duplicidad                   
            }} 
            
            
         if($cont==0){
            if($retiro!=0){
                $salida->cantidadRetirada = $retiro; 
                if(!$salida->save()){
                    $data =["estado"=>"error","mensaje"=>"La salido no se registró en el sistema"];    
                    return response($data, 402); 
                }else{
                    $salida->save(); 
                        //Log salida normal creada 
                    $ing =Ingreso::find($salida->id_ingreso);
                    $log =  new Log; 
                    $usuario = Auth::user();  
                    $log->descripcion= "Salida Normal con número de acta: ". $ing->numero_acta." con cantidad  total de retiro: ". $salida->cantidadRetirada ." fue creada por : ".$usuario->name; 
                    $log->id_usuario= $usuario ->id ; 
                    $log->save();                      
                    $data =["estado"=>"ok","mensaje"=>"La salida se registro exitosamente, Debe ser aprobada por Usuario Administrador, Productos para retirar: ".$retiro.", Productos no entregado (Riesgo): ".$riesgo];    
                    return response($data, 200);  
                } 
            }else{
                $data =["estado"=>"error","mensaje"=>"No hay ingreso con productos para realizar salida en esa solicitud"];    
                return response($data, 402); 
            }
        }else{
            $data =["estado"=>"error","mensaje"=>"Ya se generó un proceso de salida para esos productos"];    
            return response($data, 402); 
        }
                               
     }
     
     
     public function registerSalidaRiesgo(Request $req){

        $salida =  new Salida; //instancia del modelo       
                
        $salida->cedulaNombreRetira = $req->input('cedulaNombreRetira');
        $salida->nombreRetira = $req->input('nombreRetira');
        $salida->fechaSalida = $req->input('fechaSalida');      
        $salida->id_ingreso = $req->input('id_ingreso');   
        
        
         
          $riesgo = DB::table('producto')->where("id_ingreso","=", $salida->id_ingreso)->where("deleted_at","=",null )->where("riesgo","!=",'no')->sum('cantidadUnitaria');
          
          $sal = Salida::withTrashed()->get(); // se obtiene todos los objetos de la BD       
          
          $cont=0;
          foreach($sal as $fila) {         
             if ($fila->datoSalida=="destruccion" && $fila->id_ingreso==$salida->id_ingreso && $fila->deleted_at==null) {
              $cont++;  //se verifica duplicidad                   
            }} 
            
            
         if($cont==0){
            if($riesgo!=0){                
                $salida->cantidadRetirada = $riesgo; 
                if(!$salida->save()){
                    $data =["estado"=>"error","mensaje"=>"La salido no se registró en el sistema"];    
                    return response($data, 402); 
                }else{
                    $salida->datoSalida = "destruccion"; 
                    $salida->save(); 
                     //Log salida  en riesgo creada
                     $ing =Ingreso::find($salida->id_ingreso);
                     $log =  new Log; 
                     $usuario = Auth::user();  
                     $log->descripcion= "Salida Riesgo con número de acta: ". $ing->numero_acta." con cantidad  total de retiro: ". $salida->cantidadRetirada ." fue creada por : ".$usuario->name; 
                     $log->id_usuario= $usuario ->id ; 
                     $log->save();                      
                    $data =["estado"=>"ok","mensaje"=>"La salida se registro exitosamente, Debe ser aprobada por Usuario Administrador,  Productos para destruir: ".$riesgo];    
                    return response($data, 200);  
                } 
            }else{
                $data =["estado"=>"error","mensaje"=>"No hay ingreso con productos en riesgo para realizar esa salida"];    
                return response($data, 402); 
            }
        }else{
            $data =["estado"=>"error","mensaje"=>"Ya se generó un proceso de salida para esos productos"];    
            return response($data, 402); 
        }

     }


     public function salidaAll(){        
        $sal = DB::table('salida')
            ->join('ingreso', 'salida.id_ingreso', '=', 'ingreso.id_ingreso')           
            ->select('salida.id_salida','salida.cedulaNombreRetira','salida.nombreRetira','salida.salidaAprobada','salida.fechaSalida','salida.cedulaNombreOficiaSalida','salida.nombreOficiaSalida','salida.cantidadRetirada', 'salida.datoSalida','ingreso.numero_acta')
            ->where("salida.deleted_at","=",null )
            ->where("ingreso.deleted_at","=",null )
            ->get();
         
        if($sal->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{          
          return response($sal, 200);        
          }
      }

      public function salidaNormal(){        
        $sal = DB::table('salida')
            ->join('ingreso', 'salida.id_ingreso', '=', 'ingreso.id_ingreso')           
            ->select('salida.id_salida','salida.cedulaNombreRetira','salida.nombreRetira','salida.salidaAprobada','salida.fechaSalida','salida.cedulaNombreOficiaSalida','salida.nombreOficiaSalida','salida.cantidadRetirada', 'salida.datoSalida','ingreso.numero_acta')
            ->where("salida.datoSalida","=","entrega")
            ->where("salida.salidaAprobada","=","no" )
            ->where("salida.deleted_at","=",null )
            ->where("ingreso.deleted_at","=",null )
            ->get();
         
        if($sal->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{          
          return response($sal, 200);        
          }
      }

      public function salidaRiesgo(){        
        $sal = DB::table('salida')
            ->join('ingreso', 'salida.id_ingreso', '=', 'ingreso.id_ingreso')           
            ->select('salida.id_salida','salida.cedulaNombreRetira','salida.nombreRetira','salida.salidaAprobada','salida.fechaSalida','salida.cedulaNombreOficiaSalida','salida.nombreOficiaSalida','salida.cantidadRetirada', 'salida.datoSalida','ingreso.numero_acta')
            ->where("salida.datoSalida","=","destruccion")
            ->where("salida.salidaAprobada","=","no" )
            ->where("salida.deleted_at","=",null )
            ->where("ingreso.deleted_at","=",null )
            ->get();
         
        if($sal->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{          
          return response($sal, 200);        
          }
      }
     
       //método  administrador para aprobar salida normal
    public function aprobarSalidaNormal(Request $req){         
            $validator = Validator::make($req->all(), [
                'id' => 'required|numeric',        
            ]);

            if ($validator->fails()) {
                $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
                return response($data,404);                  
            }
                $id =  $req->input('id');
                
                if(Salida::find($id)!=null){

                    $ing =  Salida::find($id);    
                    

                    if($ing["datoSalida"]=="destruccion" ){
                        $data =["estado"=>"error","mensaje"=>"No se puede realizar salida, su proceso es Salida a destruccion"];            
                        return response($data,404); 
                    }
                    
                    if($ing["salidaAprobada"]=="si" ){
                        $data =["estado"=>"error","mensaje"=>"La salida ya fue aprobada"];            
                        return response($data,404); 
                    }

                    $usuario = Auth::user(); 
                    $ing->cedulaNombreOficiaSalida = $usuario->cedula;
                    $ing->nombreOficiaSalida = $usuario->name;                    
                    $ing->salidaAprobada = "si";
                    $ing->save();

                       DB::table('producto')
                           ->where('id_ingreso', "=", $ing["id_ingreso"])
                           ->where('riesgo', "=","no")
                           ->update(array('id_salida' => $ing["id_salida"],'Estado' => "Entregado"));
                          //log aprobar salida normal
                          $inge =Ingreso::find($ing->id_ingreso);
                           $log =  new Log; 
                           $usuario = Auth::user();  
                           $log->descripcion= "Salida Normal con número de acta: ". $inge->numero_acta." con cantidad  total de retiro: ". $ing->cantidadRetirada ." fue aprobada por : ".$usuario->name; 
                           $log->id_usuario= $usuario ->id ; 
                           $log->save();  
                       
                        $data =["estado"=>"ok","mensaje"=>"Salida Aprobada"];    
                        return response($data, 200);
                        

                }else{
                $data =["estado"=>"error","mensaje"=>"Salida en proceso no se encuentra en base de datos"]; 
                return response($data,404); 
                }
    }

        //método  administrador para aprobar salida normal
     public function aprobarSalidaRiesgo(Request $req){         
            $validator = Validator::make($req->all(), [
                'id' => 'required|numeric',        
            ]);

            if ($validator->fails()) {
                $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
                return response($data,404);                  
            }
                $id =  $req->input('id');
                
                if(Salida::find($id)!=null){

                    $ing =  Salida::find($id);                     
                    
                    

                    if($ing["datoSalida"]=="entrega" ){
                        $data =["estado"=>"error","mensaje"=>"No se puede realizar salida, su proceso es Salida a vendedor"];            
                        return response($data,401); 
                    }
                    
                    if($ing["salidaAprobada"]=="si" ){
                        $data =["estado"=>"error","mensaje"=>"La salida ya fue aprobada"];            
                        return response($data,401); 
                    }
                    $usuario = Auth::user();
                    $ing->cedulaNombreOficiaSalida = $usuario ->cedula;
                    $ing->nombreOficiaSalida = $usuario ->name;
                    
                        $ing->salidaAprobada = "si";
                        $ing->save();

                       DB::table('producto')
                           ->where('id_ingreso', "=", $ing["id_ingreso"])
                           ->where('riesgo', "=","si")
                           ->update(array('id_salida' => $ing["id_salida"],'Estado' => "Destruido"));
                          
                           //log aprobar salida riesgo
                          $inge =Ingreso::find($ing->id_ingreso);
                          $log =  new Log; 
                          $usuario = Auth::user();  
                          $log->descripcion= "Salida Riesgo con número de acta: ". $inge->numero_acta." con cantidad  total de retiro: ". $ing->cantidadRetirada ." fue aprobada por : ".$usuario->name; 
                          $log->id_usuario= $usuario ->id ; 
                          $log->save();  
                       
                        $data =["estado"=>"ok","mensaje"=>"Salida Aprobada"];    
                        return response($data, 200);
                        

                }else{
                $data =["estado"=>"error","mensaje"=>"Salida en proceso no se encuentra en base de datos"]; 
                return response($data,404); 
                }
    }

     
    public function deleteSalida(Request $req){      

        $validator = Validator::make($req->all(), [
            'id_salida' => 'required|numeric',        
        ]);
  
        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
            return response($data,404);                  
        }
  
            $id =  $req->input('id_salida');      
            $user = Salida::find($id);        
            if(!$user){
            $data =["estado"=>"error","mensaje"=>"La salida  no se encuentra registradada base de datos"];            
            return response($data,404);
            }else{

               if($user["datoSalida"]=="entrega"){
                    DB::table('producto')
                    ->where('id_ingreso', "=", $user["id_ingreso"])
                    ->where('riesgo', "=", "no")
                    ->update(array('id_salida' => null,'Estado' => "Bodega"));
                    $user->delete();
                     //log eliminar salida normal
                     $inge =Ingreso::find($user->id_ingreso);
                     $log =  new Log; 
                     $usuario = Auth::user();  
                     $log->descripcion= "Salida Normal con número de acta: ". $inge->numero_acta." con cantidad  total de retiro: ". $user->cantidadRetirada ." fue eliminada por : ".$usuario->name; 
                     $log->id_usuario= $usuario ->id ; 
                     $log->save();  
                    $data =["estado"=>"ok","mensaje"=>"Salida normal eliminada exitosamente"];            
                    return response($data,200);
                  }else{
                    DB::table('producto')
                    ->where('id_ingreso', "=", $user["id_ingreso"])
                    ->where('riesgo', "=", "si")
                    ->update(array('id_salida' => null,'Estado' => "Bodega"));
                    $user->delete();
                    //log eliminar salida riesgo
                    $inge =Ingreso::find($user->id_ingreso);
                    $log =  new Log; 
                    $usuario = Auth::user();  
                    $log->descripcion= "Salida riesgo con número de acta: ". $inge->numero_acta." con cantidad  total de retiro: ". $user->cantidadRetirada ." fue eliminada por : ".$usuario->name; 
                    $log->id_usuario= $usuario ->id ; 
                    $log->save();  
                    $data =["estado"=>"ok","mensaje"=>"Salida  riesgo eliminada exitosamente"];            
                    return response($data,200);

                  }
            
            
            }     
   }


   public function salidaDeleteAll(){              
        $users = Salida::withTrashed()
        ->where("deleted_at","!=",null )->get();

        if($users->isEmpty()){
            $data =["estado"=>"error","mensaje"=>"No hay salidas guardadas como borradas"];
            return response($data,404); 
        }else{
            return $users;
    }            
  }

    public function restoreSalida(Request $req){
            $validator = Validator::make($req->all(), [
            'id_salida' => 'required|numeric'       
             ]);

        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
            return response($data,404);                  
        }
            $id =  $req->input('id_salida'); 
            $user =Salida::onlyTrashed()->find($id); 
            
            if($user && $user->deleted_at !=null){//verifica que el usuario cumpla las condciones         
                Salida::onlyTrashed()->find($id)->restore();
                $res = Salida::find($id);
                $res->salidaAprobada ="no";
                $res->cedulaNombreOficiaSalida =null;
                $res->nombreOficiaSalida =null;

                $res->save();
                //log restaurar salida 
                $inge =Ingreso::find($res->id_ingreso);
                $log =  new Log; 
                $usuario = Auth::user();  
                $log->descripcion= "Salida con número de acta: ". $inge->numero_acta." con cantidad  total de retiro: ". $user->cantidadRetirada ." fue restaurada para ser aprobada por : ".$usuario->name; 
                $log->id_usuario= $usuario->id ; 
                $log->save();  
                $data =["estado"=>"ok","mensaje"=>"Salida se ha restaurado con exito, debe ser aprobada por el administrador"]; 
                return response($data,200);
            
            }else{
                $data =["estado"=>"error","mensaje"=>"No se restauro la salida,esta no se encuentra eliminada"]; 
                return response($data,404);  
            }
    }

      
    public function searchSalida($fecha){           
        $pro  = Salida::where("fechaSalida","=",$fecha)
        ->get();
        
      if(!$pro->isEmpty()){        
        return response($pro,200);
                          
        }else{
          $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
          return response($data,404);  
        }}
}
