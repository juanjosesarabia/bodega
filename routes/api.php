<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Usuario;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("usuario",function(){
  $usuario = Usuario::get();
  return $usuario;
});
 //pasamos por parametro el Request para obtener los datos por medio de post
Route::post("usuario",function(Request $request){
  return  $request->all();
  
});