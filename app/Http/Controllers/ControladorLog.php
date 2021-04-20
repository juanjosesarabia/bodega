<?php

namespace App\Http\Controllers;
use App\Log;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ControladorLog extends Controller
{
   
    public function logAll(){        
        $log = DB::table('log')
            ->join('users', 'log.id_usuario', '=', 'users.id') 
            ->orderBy('log.id_log', 'desc')          
            ->select('log.id_log','log.descripcion','log.created_at','users.cedula','users.name')
            ->where("log.deleted_at","=",null )
            ->where("users.deleted_at","=",null )
            ->get();
         
        if($log->isEmpty()){//verificar si en la bd hay registros
          $data =["estado"=>"error","mensaje"=>"No hay datos guardados"]; 
          return response($data,404);        
        }else{     
          $data =["estado"=>"ok","datos"=>$log];
          return response($data, 200);        
          }
      } //

    public function getAll(){   
      $data =[]; 
      //cuenta vendedores registrados
      $vendedor = DB::table('vendedor')->where("deleted_at","=",null)->count();
      //cuenta los ingresos registrados
      $ingreso = DB::table('ingreso')->where("deleted_at","=",null)->count();
      
      //suma todos los productos que estan en bodega
      $productos = DB::table("producto") 
                 ->where("deleted_at","=",null)
                 ->where("id_salida","=",null)
                 ->where("Estado","=",'Bodega')->get()->sum("cantidadUnitaria"); 
      
                 
      //Salidas a vendedor
      $salidasV = DB::table("salida") 
                 ->where("salidaAprobada","=","si")
                 ->where("deleted_at","=",null)
                 ->where("datoSalida","=",'entrega')                 
                 ->get()
                 ->count(); 

      //Salidas a destruccion
      $salidasD = DB::table("salida") 
                 ->where("salidaAprobada","=","si")
                 ->where("deleted_at","=",null)
                 ->where("datoSalida","=",'destruccion')                 
                 ->get()
                 ->count();      
      
      

      //ultimos tres vendedores ingresados
      $vendedores = DB::table('vendedor')                      
            ->select('cedula','nombres','apellidos','telefono')
            ->where("deleted_at","=",null)
            ->orderBy('id_vendedor', 'desc')
            ->limit(3)
            ->get();
      
      if($vendedores->isEmpty()){
        $vendedores = "Sin Datos";
            }
             
     
      //ultimos tres ingresos
      $ingresos = DB::table('vendedor')
      ->join('ingreso', 'ingreso.id_vendedor', '=', 'vendedor.id_vendedor')  
      ->select('ingreso.numero_acta','ingreso.fechaIngreso','ingreso.cantidadIngresada', 'vendedor.nombres','vendedor.apellidos')
      ->where("ingreso.deleted_at","=",null )
      ->where("vendedor.deleted_at","=",null )
      ->orderBy('ingreso.id_ingreso', 'desc')
      ->limit(3)
      ->get();     
      if($ingresos->isEmpty()){
        $ingresos = "Sin Datos";
            } 
       
      //ultimos tres salidas a vendedor
      $salidasVendedor = DB::table('salida')      
      ->select('cedulaNombreRetira','nombreRetira','fechaSalida','cantidadRetirada')
      ->where("salidaAprobada","=","si" )
      ->where("deleted_at","=",null )
      ->where("datoSalida","=","entrega")     
      ->get();
      if($salidasVendedor->isEmpty()){
        $salidasVendedor = "Sin Datos";
      }
      
      //ultimos tres salidas a destruccion

      $salidasDestruccion = DB::table('salida')      
      ->select('cedulaNombreRetira','nombreRetira','fechaSalida','cantidadRetirada')
      ->where("salidaAprobada","=","si" )
      ->where("deleted_at","=",null )
      ->where("datoSalida","=","destruccion")     
      ->get();

      if($salidasDestruccion->isEmpty()){
        $salidasDestruccion = "Sin Datos";
      }
     
      
      $data =["vendedoresN"=>$vendedor,"ingresosN"=>$ingreso,"productosN"=>$productos ,"salidaV"=>$salidasV,"salidaD"=>$salidasD,"vendedores"=>$vendedores,"ingresos"=>$ingresos,"salidasVendedor"=>$salidasVendedor,"salidasDestruccion"=>$salidasDestruccion];
     
      return response($data, 200);
      
    }

}
