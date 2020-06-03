<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ingreso;
use App\Producto;
use App\Vendedor;
use App\Log;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ControladorIngresoRequest;
class ControladorIngreso extends Controller
{
    //método para registrar ingreso
    public function registerIngreso(ControladorIngresoRequest $req){
        $ingreso =  new Ingreso; //instancia del modelo       
                
        $ingreso->cedulaNombreRecibe = $req->input('cedulaNombreRecibe');
        $ingreso->nombreRecibe = $req->input('nombreRecibe');
        $ingreso->fechaIngreso = $req->input('fechaIngreso');
        $ingreso->numero_acta = $req->input('numero_acta');  
        $ingreso->cantidadIngresada = $req->input('cantidadIngresada');  
        $ingreso->ubicacionOperativo = $req->input('ubicacionOperativo');  
        $ingreso->id_vendedor = $req->input('id_vendedor');

        $ing = Ingreso::withTrashed()->get(); // se obtiene todos los objetos de la BD       
        
        $cont=0;
        foreach($ing as $fila) {         
           if ($fila->numero_acta==$ingreso->numero_acta) {
            $cont++;  //se verifica duplicidad                   
          }}        
        
        if($cont!=0){
            $data =["estado"=>"error","mensaje"=>"El número de acta ya esta registrado"];    
            return response($data, 402); 
         }else{
          $datos= $req->input('data');
          $cont1 =0;
           foreach($datos as $fila) {  //validar datos
              if(!is_string($fila['nombre'])|| !$fila['nombre']||!is_string($fila['descripcion'])||!$fila['descripcion']||!is_numeric($fila['codigoBarra'])||!$fila['codigoBarra']||!is_numeric($fila['id_vendedor'])||!$fila['id_vendedor']||!is_numeric($fila['cantidadUnitaria'])||!$fila['cantidadUnitaria']||!is_string($fila['riesgo'])|| !$fila['riesgo']){
                $cont1++;         
              }}   
         /////////////////////////

         $vend = Vendedor::get(); // se obtiene todos los objetos de la BD       
        //codigo de barra unico con respecto al json a ingresar y los que estan en BD
        $cont6=0;
        foreach($vend as $fila) {          
           if ($fila->id_vendedor==$ingreso->id_vendedor) {
              $cont6++;  //se verifica duplicidad                   
          } 
        }

        if($cont6==0){
          $data =["estado"=>"error","mensaje"=>"El vendedor no se encuentra registrado"];    
            return response($data, 402); 
        }

        //////////////////////////

        $prod = Producto::withTrashed()->get(); // se obtiene todos los objetos de la BD       
        //codigo de barra unico con respecto al json a ingresar y los que estan en BD
        $cont2=0;
        foreach($prod as $fila) {  
          foreach($datos as $fila1) {          
           if ($fila->codigoBarra==$fila1['codigoBarra']) {
             
            $cont2++;  //se verifica duplicidad                   
          }} 
        }
         
        if($cont2!=0){
          $data =["estado"=>"error","mensaje"=>"Codigo de barra del producto ya esta guardado en la Base de datos"];    
          return response($data, 402); 
        }

        //codigo de barra unico en el json a ingresar
        $cont3=0;
        $prueba;
        foreach($datos as $fila) {  
          foreach($datos as $fila1) {          
           if ($fila['codigoBarra']==$fila1['codigoBarra']) {       
            $cont3++;  //se verifica duplicidad                   
          }} 
        }
        $prueba =count($datos);
        if($cont3!=$prueba){
          $data =["estado"=>"error","mensaje"=>"Codigo de barra de productos a ingresar estan duplicados"];    
          return response($data, 402); 
        }
         
        $suma=0;
        foreach($datos as $fila) { 
           $suma=$suma+$fila['cantidadUnitaria'];
           
        }
         
        if($suma!=$ingreso->cantidadIngresada){
          $data =["estado"=>"error","mensaje"=>"La Cantidad total en el ingreso no corresponde a la suma de cantidades unitarias "];    
          return response($data, 402); 
        }



        //////////////////////////
              if($cont1!=0){
                $data =["estado"=>"error","mensaje"=>"Los productos no cumplen los parámetros establecidos"];    
                return response($data, 402); 
              }else{
                $ingreso->save();
                $datos= $req->input('data');
                $ultimoIngreso= Ingreso::get()->last();            
                $verificacion=$this->registerProducto($datos,$ultimoIngreso->id_ingreso);//////////   
                
                //Log ingreso registrado
                $log =  new Log; 
                $usuario = Auth::user();  
                $log->descripcion= "Ingreso con número de acta: ". $ingreso->numero_acta." con cantidad  total de: ".$ingreso->cantidadIngresada ." fue realizado por : ".$usuario->name; 
                $log->id_usuario= $usuario ->id ; 
                $log->save(); 
            
                $data =["estado"=>"ok","mensaje"=>"El ingreso se registro exitosamente"];    
                return response($data, 200); 
              }

             
         }                
     } 


     protected function registerProducto($data, $id_ingreso){
             
        foreach($data as $fila) {    
           $producto =  new Producto;  
            $producto->nombre = $fila['nombre'];           
            $producto->descripcion = $fila['descripcion'];
            $producto->codigoBarra = $fila['codigoBarra'];
            $producto->id_vendedor = $fila['id_vendedor'];
            $producto->id_ingreso = $id_ingreso;
            $producto->cantidadUnitaria=$fila['cantidadUnitaria'];
            $producto->riesgo = $fila['riesgo'];             
            $producto->save();
             //se verifica duplicidad                   
           }               
                   
        if(!$producto->save()){
            $data =["estado"=>"error","mensaje"=>"Los productos no se registraron"];    
            return response($data, 402); 
         }else{
            $producto->save();
            $data =["estado"=>"ok","mensaje"=>"Los productos se registraron exitosamente"];    
            return response($data, 200);  
         }                 
     }


  
     
     
     public function ingresosAll(){      
         
          $users = DB::table('producto')
          ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor')  
          ->join('ingreso', 'ingreso.id_ingreso', '=', 'producto.id_ingreso')
          ->select('ingreso.id_ingreso','ingreso.cedulaNombreRecibe','ingreso.nombreRecibe','ingreso.fechaIngreso','ingreso.numero_acta','ingreso.cantidadIngresada','ingreso.ubicacionOperativo','producto.id_producto','producto.nombre','producto.codigoBarra','producto.cantidadUnitaria','producto.riesgo','vendedor.id_vendedor','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono')
          ->where("producto.deleted_at","=",null )
          ->where("ingreso.deleted_at","=",null )
          ->where("vendedor.deleted_at","=",null )
          ->get();
 
          if($users->isEmpty()){
              $data =["estado"=>"error","mensaje"=>"No ingresos  guardados"];
              return response($data,404); 
          }else{
              return $users;
          }            
    }


    public function ingresosAllSolo(){      
         
      $users = DB::table('vendedor')
      ->join('ingreso', 'ingreso.id_vendedor', '=', 'vendedor.id_vendedor')  
      ->select('ingreso.id_ingreso','ingreso.cedulaNombreRecibe','ingreso.nombreRecibe','ingreso.fechaIngreso','ingreso.numero_acta','ingreso.cantidadIngresada','ingreso.ubicacionOperativo','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono')
      ->where("ingreso.deleted_at","=",null )
      
      ->get();

      if($users->isEmpty()){
          $data =["estado"=>"error","mensaje"=>"No ingresos  guardados"];
          return response($data,404); 
      }else{
          return $users;
      }            
}


    
    public function ingresosDeleteAll(){              
      $users = DB::table('producto')
      ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor')  
      ->join('ingreso', 'ingreso.id_ingreso', '=', 'producto.id_ingreso')
      ->select('ingreso.id_ingreso','ingreso.cedulaNombreRecibe', 'ingreso.nombreRecibe','ingreso.fechaIngreso','ingreso.numero_acta','ingreso.cantidadIngresada','ingreso.ubicacionOperativo','producto.id_producto','producto.nombre','producto.codigoBarra','producto.cantidadUnitaria','producto.riesgo','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono')
      ->where("ingreso.deleted_at","!=",null )
      
      ->get();

      if($users->isEmpty()){
          $data =["estado"=>"error","mensaje"=>"No hay ingresos guardados como borrados"];
          return response($data,404); 
      }else{
          return $users;
      }            
    }

    public function deleteIngreso(Request $req){      

      $validator = Validator::make($req->all(), [
          'id_ingreso' => 'required|numeric',        
      ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
          return response($data,404);                  
      }

          $id =  $req->input('id_ingreso');      
          $user = Ingreso::find($id);        
          if(!$user){
          $data =["estado"=>"error","mensaje"=>"El ingreso  no se encuentra en registrado base de datos"];            
          return response($data,404);
          }else{
            $user->delete();
            //Log ingreso eliminado
            $log =  new Log; 
            $usuario = Auth::user();  
            $log->descripcion= "Ingreso con número de acta: ". $user->numero_acta." con cantidad  total de: ".$user->cantidadIngresada ." fue eliminado por : ".$usuario->name; 
            $log->id_usuario= $usuario ->id ; 
            $log->save();
            $data =["estado"=>"ok","mensaje"=>"Ingreso eliminado exitosamente"];            
            return response($data,200);
          }     
 }


     public function restoreIngreso(Request $req){
        $validator = Validator::make($req->all(), [
          'id_ingreso' => 'required|numeric'       
      ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
          return response($data,404);                  
      }
        $id =  $req->input('id_ingreso'); 
        $user =Ingreso::onlyTrashed()->find($id); 
        
          if($user && $user->deleted_at !=null){//verifica que el usuario cumpla las condciones         
          Ingreso::onlyTrashed()->find($id)->restore();
          //Log ingreso restaurado
          $log =  new Log; 
          $usuario = Auth::user();  
          $log->descripcion= "Ingreso con número de acta: ". $user->numero_acta." con cantidad  total de: ".$user->cantidadIngresada ." fue restaurado por : ".$usuario->name; 
          $log->id_usuario= $usuario ->id ; 
          $log->save();
          $data =["estado"=>"ok","mensaje"=>"Producto restaurado con exito"]; 
          return response($data,200);
        
        }else{
          $data =["estado"=>"error","mensaje"=>"No se restauro el producto,este no se encuentra eliminado"]; 
          return response($data,404);  
        }
    }

    public function editIngreso(ControladorIngresoRequest $req){
      $validator = Validator::make($req->all(), [
        'id_ingreso' => 'required|numeric',        
       ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
          return response($data,404);                  
       }
      $id =  $req->input('id_ingreso');               
      
      if(!Ingreso::find($id)){//verificar si en la bd hay registros
            $data =["estado"=>"error","mensaje"=>"No se encontró dato de producto a modificar"]; 
            return response($data,404);        
      }else{  
        $ingresoV=Ingreso::withTrashed()->where("id_ingreso","!=",$id)->get();
         
        $ingreso = Ingreso::find($id); //instancia del modelo  
          $ingreso->cedulaNombreRecibe = $req->input('cedulaNombreRecibe');
          $ingreso->nombreRecibe = $req->input('nombreRecibe');
          $ingreso->fechaIngreso = $req->input('fechaIngreso');
          $ingreso->numero_acta = $req->input('numero_acta');  
          $ingreso->cantidadIngresada = $req->input('cantidadIngresada');  
          $ingreso->ubicacionOperativo = $req->input('ubicacionOperativo');
          $cont=0;
        foreach($ingresoV as $fila) {         
          if ($fila->numero_acta==$ingreso->numero_acta ) {
            $cont++;  //se verifica duplicidad           
          }}
          
          if($cont==0){
            //Log ingreso modificado
            $log =  new Log; 
            $usuario = Auth::user();  
            $log->descripcion= "Ingreso con número de acta: ". $ingreso->numero_acta." con cantidad  total de: ".$ingreso->cantidadIngresada ." fue editado por : ".$usuario->name; 
            $log->id_usuario= $usuario ->id ; 
            $log->save();
            $ingreso->save();
            $data =["estado"=>"ok","mensaje"=>"Ingreso modificado con exito"];    
            return response($data, 200);
           }else{
            $data =["estado"=>"error","mensaje"=>"El número de acta ya esta registrada"];            
          return response($data,404);  
              }                                    
                      
      } 
  }  
     
    /// metodo para buscar ingreso por numero de acta
    public function searchIngreso($acta){           
      $pro  = DB::table('ingreso')          
      ->select('id_ingreso','cedulaNombreRecibe','nombreRecibe','fechaIngreso','numero_acta','cantidadIngresada','ubicacionOperativo')
      ->where('numero_acta',"=", $acta)
      ->where("deleted_at","=",null )
      ->get();
      
    if(!$pro->isEmpty()){        
      return response($pro,200);
                        
      }else{
        $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
        return response($data,404);  
      }}
}
