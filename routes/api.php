<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Api de control de datos de bodega de espacio publico y movilidad
|
*/
Route::get('getUser','ControladorUsuario@userAll' );// obtener todos los usuarios registrados
Route::post("validateUser",'ControladorUsuario@validateUser');//validar ingreso de usario
Route::post("registerUser",'ControladorUsuario@registerUser');//Registrar usuario Nuevo
Route::put("editUser",'ControladorUsuario@editUser'); //editar usuario
Route::get('searchUser/{id}', 'ControladorUsuario@searchUser')->where('id', '[0-9]+');//Buscar usuario
Route::put('resetPassword', 'ControladorUsuario@resetPassword');//cambiar contraseña
Route::put('cambiarTipo', 'ControladorUsuario@cambiarTipo');//cambiar tipo de usuario

Route::delete('deleteUser/{id}','ControladorUsuario@deletetUser')->where('id', '[0-9]+'); // eliminar usuario de la tabla usuarios y asignalos al log

