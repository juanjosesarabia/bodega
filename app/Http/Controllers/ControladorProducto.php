<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Log;
use Illuminate\Support\Facades\Auth; 
use App\Producto;
use App\Ingreso;
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
        $producto->riesgo = $req->input('riesgo'); 
        $producto->cantidadUnitaria= $req->input('cantidadUnitaria');
        $acta= $req->input('numero_acta');

         $validator = Validator::make($req->all(), [
          'numero_acta' => 'required|numeric',        
          ]);

         if ($validator->fails()) {
            $data =["estado"=>"error","mensaje"=>"Número de acta esta  vacío o no es numerico"];            
            return response($data,404);                  
          }
        
        $ingreso = Ingreso::where("numero_acta","=",$acta)->get();

        if($ingreso->isEmpty()){
          $data =["estado"=>"error","mensaje"=>"El número de acta no se encuentra registrado en la bases de datos"];    
          return response($data, 404); 
         }else{
          foreach($ingreso as $fila){
            $producto->id_vendedor =$fila->id_vendedor;
            $producto->id_ingreso=$fila->id_ingreso;
            $verificaProducto = Producto::where("id_ingreso","=",$fila->id_ingreso)->where("riesgo","=",$producto->riesgo)->get();
           }
           $cont=0;
          foreach($verificaProducto as $fila){
            if($fila->id_salida!=null){
              $cont++;
              }}

           if($cont!=0){
              $data =["estado"=>"error","mensaje"=>"No se puede agregar productos a un ingreso que ya se le realizó salida"];    
              return response($data, 404); 
            }else{
              $prod = Producto::withTrashed()->get(); // se obtiene todos los objetos de la BD    
                $cont2=0;  
                foreach($prod as $fila5){//validar codigo de barra no este usado
                  if ($fila5->codigoBarra==$producto->codigoBarra) {             
                    $cont2++;  //se verifica duplicidad                   
                  }} 

                 if($cont2!=0){
                    $data =["estado"=>"error","mensaje"=>"No se puede agregar producto, codigo de barra usado"];    
                    return response($data, 404);
                  }else{  

                   $suma = DB::table('producto')->where('id_salida','=', null)->where('id_ingreso','=',$producto->id_ingreso)->where('deleted_at','=', null)->sum('cantidadUnitaria');                
                    
                    $producto->save();//guardar producto 
                    $valorNuevo=$producto->cantidadUnitaria+$suma;//suma de valores
                    
                    $paraIngreso = Ingreso::where("id_ingreso","=",$producto->id_ingreso)->first();//busco el ingreso
                    $paraIngreso->cantidadIngresada= $valorNuevo; //asigno nuevo total
                    $paraIngreso->save();
                    
                    //Log registrar producto
                    $log =  new Log; 
                    $usuario = Auth::user();  
                    $log->descripcion= "Producto : ".$producto->nombre." con codigo de barra ".$producto->codigoBarra ." registrado y agregado a número de acta ".$acta."  por : ".$usuario->name; 
                    $log->id_usuario= $usuario ->id ; 
                    $log->save(); 
                    
                  $data =["estado"=>"ok","mensaje"=>"Los productos se registraron exitosamente"];    
                  return response($data, 200);  
                }
            } 
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

            $produc=producto::withTrashed()->where("id_producto","!=",$id)->get();//buscar productor para verificar codigo barra
            $producto = Producto::find($id);//buscar producto a modificar

            $ingreso = Ingreso::where("id_ingreso","=",$producto->id_ingreso)->first(); //buscar ingreso
             
            $producto->nombre = $req->input('nombre');//captura todos los datos del request
            $producto->descripcion = $req->input('descripcion');
            $producto->codigoBarra = $req->input('codigoBarra');
            $producto->cantidadUnitaria= $req->input('cantidadUnitaria');            
            $producto->riesgo = $req->input('riesgo');  

           
              $producto->id_vendedor =$ingreso->id_vendedor; //asignamos id vendedor al producto  a modificar         
              $verificaProducto = Producto::where("id_ingreso","=",$ingreso->id_ingreso)->where("riesgo","=",$producto->riesgo)->get();//veriifcamos salida
            
             $cont1=0;
            foreach($verificaProducto as $fila){
              if($fila->id_salida!=null){
                $cont1++;
                }}

            if($cont1!=0){
                  $data =["estado"=>"error","mensaje"=>"No puedes editar un producto al cual se le dio salida"];    
                  return response($data, 404);
            }else{
              $cont=0;
              foreach($produc as $fila) {         
                if ($fila->codigoBarra==$producto->codigoBarra ) {
                  $cont++;  //se verifica duplicidad           
                }}
              if($cont==0){                
  
                $suma = DB::table('producto')->where('id_salida','=', null)->where('id_producto','!=',$id)->where('id_ingreso','=',$producto->id_ingreso)->where('deleted_at','=', null)->sum('cantidadUnitaria');                
                    
                $producto->save();//guardar producto 
                $valorNuevo=$producto->cantidadUnitaria+$suma;//suma de valores
                
                $paraIngreso = Ingreso::where("id_ingreso","=",$producto->id_ingreso)->first();//busco el ingreso
                $paraIngreso->cantidadIngresada= $valorNuevo; //asigno nuevo total
                $paraIngreso->save();               
  
  
                //Log editar producto
               $log =  new Log; 
               $usuario = Auth::user();  
               $log->descripcion= "Producto : ".$producto->nombre." con codigo de barra ".$producto->codigoBarra ." fue editado por : ".$usuario->name; 
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
                           
              $paraIngreso = Ingreso::where("id_ingreso","=",$user->id_ingreso)->first();//busco el ingreso
              $suma = DB::table('producto')->where('id_salida','=', null)->where('id_producto','!=',$id)->where('id_ingreso','=',$user->id_ingreso)->where('deleted_at','=', null)->sum('cantidadUnitaria'); 
             
              $user->delete();
                          
            
              $paraIngreso->cantidadIngresada= $suma; //asigno nuevo total
              $paraIngreso->save();       
           
            //Log eliminar producto
            $log =  new Log; 
            $usuario = Auth::user();  
            $log->descripcion= "Producto : ".$user->nombre." con codigo de barra ".$user->codigoBarra ." fue eliminado por : ".$usuario->name.", su ingreso total es ".$suma; 
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

          $suma = DB::table('producto')->where('id_salida','=', null)->where('id_ingreso','=',$user->id_ingreso)->where('deleted_at','=', null)->sum('cantidadUnitaria');                
                    
               
                
                $paraIngreso = Ingreso::where("id_ingreso","=",$user->id_ingreso)->first();//busco el ingreso
                $paraIngreso->cantidadIngresada=  $suma; //asigno nuevo total
                $paraIngreso->save();               
  
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
