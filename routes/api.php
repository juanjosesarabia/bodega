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
Route::get('searchUserCc/{cc}', 'ControladorUsuario@searchUserCc')->where('cc', '[0-9]+');//Buscar usuario
Route::put('resetPasswordAd', 'ControladorUsuario@resetPasswordAd');//cambiar contraseña administrador
Route::put('cambiarTipo', 'ControladorUsuario@cambiarTipo');//cambiar tipo de usuario
Route::delete('deleteUser','ControladorUsuario@deletetUser'); // Estado eliminado para usuario
Route::get('getUserDelete','ControladorUsuario@userAllDelete');//obtener usuarios eliminados
Route::post('restoreUser','ControladorUsuario@restoreUser');//restaurar los usuarios borrados


Route::post('registerVendedor', 'ControladorVendedor@registerVendedor');//Registrar Vendedor Nuevo
Route::get('getVendedores', 'ControladorVendedor@vendedoresAll');//Obtener Vendedores sin eliminar
Route::put("editVendedor",'ControladorVendedor@editVendedor'); //editar vendedor
Route::delete('deleteVendedor','ControladorVendedor@deleteVendedor'); // Estado eliminado para vendedor
Route::get('getVendedorDelete','ControladorVendedor@vendedorAllDelete');//obtener vendedores eliminados
Route::get('searchVendedor/{id}', 'ControladorVendedor@searchVendedor')->where('id', '[0-9]+');//Buscar un vendedor
Route::get('searchVendedorCc/{cc}', 'ControladorVendedor@searchVendedorCc')->where('cc', '[0-9]+');//Buscar un vendedor
Route::post('restoreVendedor','ControladorVendedor@restoreVendedor');//Obtener vendedores eliminados

Route::post('registerProducto', 'ControladorProducto@registerproducto');//Registrar Producto Nuevo
Route::get('getProducto', 'ControladorProducto@productoAll');//Obtener todos los productos
Route::get('getsProducto', 'ControladorProducto@productosAll');//Obtener todos los productos con vendedores
Route::get('getsProducDelete', 'ControladorProducto@productosDeleteAll');//Obtener todos los productos con vendedores y eliminados
Route::put("editProducto",'ControladorProducto@editProducto'); //editar producto
Route::delete('deleteProducto','ControladorProducto@deleteProducto'); // Estado eliminado para producto
Route::get('searchProducto/{id}', 'ControladorProducto@searchProducto')->where('id', '[0-9]+');//Buscar un producto
Route::get('searchProductoC/{codigo}', 'ControladorProducto@searchProductoC')->where('codigo', '[0-9]+');
Route::get('searchProductoE/{estado}', 'ControladorProducto@searchProductoE');
Route::post('restoreProducto','ControladorProducto@restoreProducto');//Obtener producto eliminados

Route::post('registerIngreso', 'ControladorIngreso@registerIngreso');//Registrar ingreso con productos
Route::get('getIngreso', 'ControladorIngreso@ingresosAll');//Obtener todos los ingresos
Route::get('getIngreSolo', 'ControladorIngreso@ingresosAllSolo');//Obtener todos los ingresos
Route::get('getIngresoDelete', 'ControladorIngreso@ingresosDeleteAll');//Obtener todos los ingresos eliminados
Route::delete('deleteIngreso','ControladorIngreso@deleteIngreso'); 
Route::post('restoreIngreso','ControladorIngreso@restoreIngreso');//Restaurar ingresos eliminados
Route::put("editIngreso",'ControladorIngreso@editIngreso'); //editar ingreso

Route::post('registerSalida', 'ControladorSalida@registerSalidaNormal');//Registrar ingreso con productos