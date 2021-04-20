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

Route::post('validateUser', 'UsuarioPrueba@validateUser');//validar ingreso de usario
Route::post('registerUser', 'UsuarioPrueba@registerUser');//Registrar usuario Nuevo
Route::group(['middleware' => 'auth:api'], function(){   // Validacion con token para acceder a estas rutas 
    
    Route::get('getUser','UsuarioPrueba@userAll' );// obtener todos los usuarios registrados 
    Route::put("editUser",'UsuarioPrueba@editUser'); //editar usuario
    Route::get('searchUser/{id}', 'UsuarioPrueba@searchUser')->where('id', '[0-9]+');//Buscar usuario
    Route::get('searchUserCc/{cc}', 'UsuarioPrueba@searchUserCc')->where('cc', '[0-9]+');//Buscar usuario
    Route::put('resetPasswordAd', 'UsuarioPrueba@resetPasswordAd');//cambiar contraseña administrador
    Route::put('cambiarTipo', 'UsuarioPrueba@cambiarTipo');//cambiar tipo de usuario
    Route::delete('deleteUser','UsuarioPrueba@deletetUser'); // Estado eliminado para usuario
    Route::get('getUserDelete','UsuarioPrueba@userAllDelete');//obtener usuarios eliminados
    Route::post('restoreUser','UsuarioPrueba@restoreUser');//restaurar los usuarios borrados

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
    Route::get('getIngreso', 'ControladorIngreso@ingresosAll');//Obtener todos los ingresos individuales
    Route::get('getIngreSolo', 'ControladorIngreso@ingresosAllSolo');//Obtener todos los ingresos unitarios
    Route::get('getIngresoDelete', 'ControladorIngreso@ingresosDeleteAll');//Obtener todos los ingresos eliminados
    Route::get('getIngresosEliminados', 'ControladorIngreso@ingresosEliminados');//Obtener todos los ingresos eliminados solos
    Route::get('getIngreVer', 'ControladorIngreso@ingresosVer');//todos los ingresos con sus productos
    Route::get('getIngreOne', 'ControladorIngreso@ingresosOne');//Un ingreso con sus productos
     
    Route::delete('deleteIngreso','ControladorIngreso@deleteIngreso'); //Eliminar ingreso
    Route::post('restoreIngreso','ControladorIngreso@restoreIngreso');//Restaurar ingresos eliminados
    Route::put("editIngreso",'ControladorIngreso@editIngreso'); //editar ingreso
    Route::get('searchIngreso/{acta}', 'ControladorIngreso@searchIngreso')->where('acta', '[0-9]+');//Buscar ingreso por acta
    Route::get('getIngresoParaSalida', 'ControladorIngreso@ingresosParaSalida');//Obtener todos los ingresos para generar salida

    Route::post('registerSalida', 'ControladorSalida@registerSalidaNormal');//Registrar salida productos sin riesgo
    Route::post('registerSalidaRiesgo', 'ControladorSalida@registerSalidaRiesgo');// Registrar salida Riesgo
    Route::get('getSalida', 'ControladorSalida@salidaAll'); //Obtener todos los datos de salida
    Route::get('getSalidaNormal', 'ControladorSalida@salidaNormal'); //Obtener salida de productos normales
    Route::get('getSalidaRiesgo', 'ControladorSalida@salidaRiesgo'); //Obtener salida de productos con riesgo
    Route::get('getSalidaDelete', 'ControladorSalida@salidaDeleteAll'); //obtener salida eliminada
    Route::put('aprobarSalidaN', 'ControladorSalida@aprobarSalidaNormal'); //Aprobar Salida Normal
    Route::put('aprobarSalidaR', 'ControladorSalida@aprobarSalidaRiesgo'); //Aprobar Salida Riesgo
    Route::delete('deleteSalida','ControladorSalida@deleteSalida'); //Eliminar ingreso
    Route::post('restoreSalida','ControladorSalida@restoreSalida'); //Eliminar ingreso
    Route::get('searchSalida/{fecha}', 'ControladorSalida@searchSalida');//Buscar salida por fecha

    Route::get('getLog', 'ControladorLog@logAll'); //Obtener todos los datos de salida

    Route::get('getData','ControladorLog@getAll');//obtner data da las dashboard
});
