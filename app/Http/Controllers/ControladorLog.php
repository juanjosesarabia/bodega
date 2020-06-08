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
          return response($log, 200);        
          }
      } //
}
