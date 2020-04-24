<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ingreso;
use App\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ControladorIngresoRequest;
class ControladorIngreso extends Controller
{
    //método para registrar ingreso
    public function registerIngreso(Request $req){
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
            $ingreso->save();
            $datos= $req->input('data');
            $ultimoIngreso= Ingreso::get()->last();            
            $verificacion=$this->registerProducto($datos,$ultimoIngreso->id_ingreso);//////////
           
            return $verificacion;                     
            
           
            $data =["estado"=>"ok","mensaje"=>"El ingreso se registro exitosamente"];    
            return response($data, 200);  
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
          ->select('ingreso.id_ingreso','ingreso.nombreRecibe','ingreso.fechaIngreso','ingreso.numero_acta','ingreso.cantidadIngresada','ingreso.ubicacionOperativo','producto.id_producto','producto.nombre','producto.codigoBarra','producto.riesgo','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono')
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

    
    public function ingresosDeleteAll(){              
      $users = DB::table('producto')
      ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor')  
      ->join('ingreso', 'ingreso.id_ingreso', '=', 'producto.id_ingreso')
      ->select('ingreso.id_ingreso','ingreso.nombreRecibe','ingreso.fechaIngreso','ingreso.numero_acta','ingreso.cantidadIngresada','ingreso.ubicacionOperativo','producto.id_producto','producto.nombre','producto.codigoBarra','producto.riesgo','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono')
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
          $data =["estado"=>"ok","mensaje"=>"Producto restaurado con exito"]; 
          return response($data,200);
        
        }else{
          $data =["estado"=>"error","mensaje"=>"No se restauro el producto,este no se encuentra eliminado"]; 
          return response($data,404);  
        }
    }

    public function editIngreso(Request $req){
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
          $ingreso = Ingreso::find($id); //instancia del modelo  
          $ingreso->cedulaNombreRecibe = $req->input('cedulaNombreRecibe');
          $ingreso->nombreRecibe = $req->input('nombreRecibe');
          $ingreso->fechaIngreso = $req->input('fechaIngreso');
          $ingreso->numero_acta = $req->input('numero_acta');  
          $ingreso->cantidadIngresada = $req->input('cantidadIngresada');  
          $ingreso->ubicacionOperativo = $req->input('ubicacionOperativo');                                           
          $ingreso->save();
          $data =["estado"=>"ok","mensaje"=>"Ingreso modificado con exito"];    
          return response($data, 200);            
      } 
  }  
     
}
