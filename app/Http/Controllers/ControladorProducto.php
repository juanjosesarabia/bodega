<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Log;
use Illuminate\Support\Facades\Auth; 
use App\Producto;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ControladorProductoRequest;


class ControladorProducto extends Controller
{
  
    //método para registrar Producto
    public function registerProducto(ControladorProductoRequest $req){
        $producto =  new Producto; //instancia del modelo       
                
        $producto->nombre = $req->input('nombre');
        $producto->descripcion = $req->input('descripcion');
        $producto->codigoBarra = $req->input('codigoBarra');
        $producto->id_vendedor = $req->input('id_vendedor'); 
        $producto->riesgo = $req->input('riesgo'); 
        $producto->cantidadUnitaria= $req->input('cantidadUnitaria');
        
        
        
        if(!$producto->save()){
            $data =["estado"=>"error","mensaje"=>"Los productos no se registraron"];    
            return response($data, 402); 
         }else{
            $producto->save();
             //Log registrar producto
             $log =  new Log; 
             $usuario = Auth::user();  
             $log->descripcion= "Producto : ".$producto->nombre." con codigo de barra ".$producto->codigoBarra ." registrado por : ".$usuario->name; 
             $log->id_usuario= $usuario ->id ; 
             $log->save(); 
            $data =["estado"=>"ok","mensaje"=>"Los productos se registraron exitosamente"];    
            return response($data, 200);  
         }                 
     } 
     
     //método para obtener todos los datos registrados de productos.
    public function productoAll(){
        $pro = Producto::all();
        $datos =[];
         
        if($pro->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{
          foreach($pro as $fila) { 
            $datos1 = array("id"=>$fila->id_producto,"nombre"=>$fila->nombre,"descripcion"=>$fila->descripcion,"codigo de barra"=> $fila->codigoBarra,'Cantidad Unitaria'=>$fila->cantidadUnitaria,"Riesgo"=> $fila->riesgo);   
            array_push($datos, $datos1);                            
           }
          return response($datos, 200);        
          }
      }


       //método para obtener todos los datos registrados de productos con vendedores
    public function productosAll(){
        $users = DB::table('producto')
            ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor') 
            ->join('ingreso', 'ingreso.id_ingreso', '=', 'producto.id_ingreso')           
            ->select('producto.id_producto','producto.nombre','producto.codigoBarra','producto.cantidadUnitaria','producto.riesgo','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono','ingreso.fechaIngreso','ingreso.numero_acta')
            ->where("producto.deleted_at","=",null )
            ->get();

            if($users->isEmpty()){
                $data =["estado"=>"error","mensaje"=>"No hay datos guardados"];
                return response($data,404); 
            }else{
                return $users;
            }            
      }

     //método para editar producto registrado
     public function editProducto(ControladorProductoRequest $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric',        
         ]);
  
        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
            return response($data,404);                  
         }
        $id =  $req->input('id');               
        
        if(!Producto::find($id)){//verificar si en la bd hay registros
              $data =["estado"=>"error","mensaje"=>"No se encontró dato de producto a modificar"]; 
              return response($data,404);        
        }else{   

            $produc=producto::withTrashed()->where("id_producto","!=",$id)->get();
            $producto = Producto::find($id);             
            $producto->nombre = $req->input('nombre');
            $producto->descripcion = $req->input('descripcion');
            $producto->codigoBarra = $req->input('codigoBarra');
            $producto->cantidadUnitaria= $req->input('cantidadUnitaria');
            $producto->id_vendedor = $req->input('id_vendedor'); 
            $producto->riesgo = $req->input('riesgo');  
             $cont=0;
            foreach($produc as $fila) {         
              if ($fila->codigoBarra==$producto->codigoBarra ) {
                $cont++;  //se verifica duplicidad           
              }}
            if($cont==0){
              $producto->save();
              //Log editar producto
             $log =  new Log; 
             $usuario = Auth::user();  
             $log->descripcion= "Producto : ".$producto->nombre." con codigo de barra ".$producto->codigoBarra ."fue editado por : ".$usuario->name; 
             $log->id_usuario= $usuario ->id ; 
             $log->save(); 
              $data =["estado"=>"ok","mensaje"=>"Producto modificado con exito"];    
              return response($data, 200);
            }else{
              $data =["estado"=>"error","mensaje"=>" Código de barra ya esta registrado"]; 
              return response($data,401); 
            }

             
        } 
    }  
    
    //método para elimiar producto registrado
   public function deleteProducto(Request $req){      

        $validator = Validator::make($req->all(), [
            'id' => 'required|numeric',        
        ]);

        if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"id esta  vacío o no es numerico"];            
            return response($data,404);                  
        }

            $id =  $req->input('id');      
            $user = Producto::find($id);        
            if(!$user){
            $data =["estado"=>"error","mensaje"=>"Producto  no se encuentra en registrado base de datos"];            
            return response($data,404);
            }else{
            $user->delete();
            //Log eliminar producto
            $log =  new Log; 
            $usuario = Auth::user();  
            $log->descripcion= "Producto : ".$user->nombre." con codigo de barra ".$user->codigoBarra ." fue eliminado por : ".$usuario->name; 
            $log->id_usuario= $usuario ->id ; 
            $log->save(); 
            $data =["estado"=>"ok","mensaje"=>"Producto eliminado exitosamente"];            
            return response($data,200);
            }     
   }

      //método para obtener todos los datos registrados de productos con vendedores
      public function productosDeleteAll(){
        $users = DB::table('producto')
            ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor')           
            ->select('producto.id_producto','producto.nombre','producto.codigoBarra','producto.cantidadUnitaria','producto.riesgo','producto.deleted_at','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono')
            ->where("producto.deleted_at","!=",null)
            ->get();

            if($users->isEmpty()){
                $data =["estado"=>"error","mensaje"=>"No hay datos guardados"];
                return response($data,404); 
            }else{
                return $users;
            }            
      }


       //método para buscar productos registrado
    public function searchProducto($id){
        if(Producto::find($id)){
            $user = Producto::find($id);
            $datos1 = array("id"=>$user->id_producto,"nombres"=>$user->nombre,"descripcion"=>$user->descripcion,"Codigo Barra"=> $user->codigoBarra,"Cantidad Unitaria"=>$user->cantidadUnitaria);
            return response($datos1,200);
        }else{
          $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
          return response($data,404);  
        }}

       /// metodo para buscar porducto por codigo de barra
        public function searchProductoC($codigo){           
            $pro  = DB::table('producto')
            ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor') 
            ->join('ingreso', 'ingreso.id_ingreso', '=', 'producto.id_ingreso')           
            ->select('producto.id_producto','producto.nombre','producto.codigoBarra','producto.cantidadUnitaria','producto.riesgo','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono','ingreso.fechaIngreso','ingreso.numero_acta')
            ->where('codigoBarra',"=", $codigo)
            ->where("producto.deleted_at","=",null )
            ->get();
            
          if(!$pro->isEmpty()){        
            return response($pro,200);
                              
            }else{
              $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
              return response($data,404);  
            }}

         /// metodo para buscar porducto por codigo de barra
         public function searchProductoE($estado){           
            $pro  = DB::table('producto')
            ->join('vendedor', 'vendedor.id_vendedor', '=', 'producto.id_vendedor') 
            ->join('ingreso', 'ingreso.id_ingreso', '=', 'producto.id_ingreso')           
            ->select('producto.id_producto','producto.nombre','producto.codigoBarra','producto.cantidadUnitaria','producto.riesgo','producto.Estado','vendedor.cedula', 'vendedor.nombres','vendedor.apellidos','vendedor.telefono','ingreso.fechaIngreso','ingreso.numero_acta')
            ->where('Estado',"=", $estado)
            ->where("producto.deleted_at","=",null )
            ->get();
            
          if(!$pro->isEmpty()){        
            return response($pro,200);
                              
            }else{
              $data =["estado"=>"error","mensaje"=>"No se encontraron datos"]; 
              return response($data,404);  
            }}


      //método restaurar producto borrado
      public function restoreProducto(Request $req){
        $validator = Validator::make($req->all(), [
          'id' => 'required|numeric'       
      ]);

      if ($validator->fails()) {
          $data =["estado"=>"error","mensaje"=>"id esta en vacío o no es numerico"];            
          return response($data,404);                  
      }
        $id =  $req->input('id'); 
        $user =Producto::onlyTrashed()->find($id); 
        
          if($user && $user->deleted_at !=null){//verifica que el usuario cumpla las condciones         
          Producto::onlyTrashed()->find($id)->restore();
          //Log restaurar producto
          $log =  new Log; 
          $usuario = Auth::user();  
          $log->descripcion= "Producto : ".$user->nombre." con codigo de barra ".$user->codigoBarra ." fue restaurado por : ".$usuario->name; 
          $log->id_usuario= $usuario ->id ; 
          $log->save(); 
          $data =["estado"=>"ok","mensaje"=>"Producto restaurado con exito"]; 
          return response($data,200);
        
        }else{
          $data =["estado"=>"error","mensaje"=>"No se restauro el producto,este no se encuentra eliminado"]; 
          return response($data,404);  
        }
    }
  


}
