<?php

/*Route::auth();

Route::get('login', 'AuthController@getLogin');
Route::post('login', 'AuthController@postLogin');


Route::controllers([
   'password' => 'Auth\PasswordController',
]);
	
Route::group(['before' => 'auth'], function()
{
    Route::get('home', 'HomeController@getIndex');

	Route::get('/', function () {
    	return View::make('pages.home');
	});
});*/

/*API'S*/
Route::get('api/pedidos/listarPedidos' , 'Api\PedidoController@listarPedidos');

Route::get('api/pedidos/validarEstadoPedidos', 'Api\PedidoController@validarEstadoPedidos');

Route::get('api/pedidos/envioPedidosIntegral', 'Api\PedidoController@envioPedidosIntegral');

Route::get('api/pedidos/buscarTrackingNumberPedidosIntegral', 'Api\PedidoController@buscarTrackingNumberPedidosIntegral');

Route::get('api/pedidos/buscarTrackingNumberPedidosUrbano', 'Api\PedidoController@buscarTrackingNumberPedidosUrbano');

Route::get('api/pedidos/envioMailAvisoRetiroSucursal', 'Api\PedidoController@envioMailAvisoRetiroSucursal');

Route::get('api/stock/actualizacionStock', 'Api\StockController@actualizacionStock');

Route::get('api/precio/actualizar_precio_stock', 'Api\PrecioController@actualizar_precio_stock');

Route::get('api/precio/subidaMasivaPrecios', 'Api\PrecioController@subidaMasivaPrecios');

Route::get('api/precio/validarPrecioPreseaVtex', 'Api\PrecioController@validarPrecioPreseaVtex');

Route::get('api/product/subirAllProducts', 'Api\ProductController@subirAllProducts');

Route::get('api/product/productosSimilares', 'Api\ProductController@productosSimilares');

Route::get('api/product/actualizarAtributos', 'Api\ProductController@actualizarAtributos');

Route::get('api/product/skuSinImagenes', 'Api\ProductController@skuSinImagenes');

Route::get('api/product/forzarSubidaMasivaProductos', 'Api\ProductController@forzarSubidaMasivaProductos');

Route::get('api/product/forzarsubidaProductosAutomotor', 'Api\ProductController@forzarsubidaProductosAutomotor');

Route::get('api/product/subidaProductosSkuAuxiliar', 'Api\ProductController@forzarsubidaProductosSkuAuxiliar');

Route::get('api/product/actualizarImagen', 'Api\ProductController@actualizarImagen');


Route::group(['middleware' => ['web']], function () {
     
    Route::get('/', ['middleware'=> 'auth', 'uses' => 'HomeController@getIndex']);
    Route::get('home', ['middleware'=> ['auth'], 'uses' => 'HomeController@getIndex']);  

    /*Rutass del menú izquierdo*/
    Route::get('catalogo', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@getIndex']);
    Route::get('pedidos', ['middleware'=> ['auth'], 'uses' => 'PedidoController@getIndex']);
    Route::get('stock', ['middleware'=> ['auth'], 'uses' => 'StockController@getIndex']);

    /*Rutas de Pedidos*/
    Route::get('pedidos/listarPedidos', ['middleware'=> ['auth'], 'uses' => 'PedidoController@listarPedidos']);
    Route::get('pedidos/detalle/{orderId}', ['middleware'=> ['auth'], 'uses' => 'PedidoController@detallePedido']);

    Route::Post('pedidos/cancelar_pedido', ['middleware'=> ['auth'], 'uses' => 'PedidoController@cancelar_pedido']);



    /*Ruta de Catalogo*/
    Route::get('catalogo/detalle/{codigo}', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@productoDetalle']);
    

    /*Ruta de Catalogo de Ventas*/
    Route::get('catalogoVentas', ['middleware'=> ['auth'], 'uses' => 'CatalogoVentasController@getIndex']);

    Route::get('catalogoVentas/detalle/{codigo}', ['middleware'=> ['auth'], 'uses' => 'CatalogoVentasController@productoDetalle']);


    /*Informes*/
    Route::get('informes', ['middleware'=> ['auth'], 'uses' => 'InformesController@getIndex']);
    Route::get('informes/exportarSkuActivos', ['middleware'=> ['auth'], 'uses' => 'InformesController@exportarSkuActivos']);

    Route::get('informes/exportarTpAdic', ['middleware'=> ['auth'], 'uses' => 'InformesController@exportarTpAdic']);

    Route::get('informes/exportarCompPreciosPreseaVtex', ['middleware'=> ['auth'], 'uses' => 'InformesController@exportarCompPreciosPreseaVtex']);

    Route::get('informes/skuSinImagenes', ['middleware'=> ['auth'], 'uses' => 'InformesController@skuSinImagenes']);

    
    /*AJAX*/ 

    Route::get('catalogoVentas/showCatalogoVentasDataTable/', ['middleware'=> ['auth'], 'uses' => 'CatalogoVentasController@showCatalogoVentasDataTable']);

    /*Pedidos*/
    Route::get('pedidos/showPedidosDataTable/', ['middleware'=> ['auth'], 'uses' => 'PedidoController@showPedidosDataTable']);

    /*Stock*/
    Route::get('/stock', ['middleware'=> ['auth'], 'uses' => 'StockController@getIndex']);
    Route::get('/stock/showStockDataTable', ['middleware'=> ['auth'], 'uses' => 'StockController@showStockDataTable']);


    /*Rutas de Catalogo*/
    Route::get('catalogo/showCatalogoDataTable/', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@showCatalogoDataTable']);

    Route::get('catalogo/subirProducto/{sku}', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@subirProductoBySKu']);

    Route::post('catalogo/subirProductos', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@subirProductos']);

    

    Route::get('catalogo/activarProducto/{sku}', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@activarProductoBySku']);

    Route::post('catalogo/activarProductos', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@activarProductos']);

    Route::get('catalogo/inactivarProducto/{sku}', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@inactivarProductoBySku']);

    Route::post('catalogo/inactivarProductos', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@inactivarProductos']);

    Route::get('catalogo/actualizarAtributos/{sku}', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@actualizarAtributos']);

    Route::post('catalogo/actAtributos', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@actAtributos']);

    Route::get('catalogo/actualizarImagen/{sku}', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@actualizarImagen']);
    Route::post('catalogo/actImagenes', ['middleware'=> ['auth'], 'uses' => 'CatalogoController@actImagenes']);

    Route::get('login', 'AuthController@getLogin');
    Route::post('login', 'AuthController@postLogin');
    Route::get('logout', 'AuthController@getLogout');

    // Registration Routes...
    Route::get('user/register', ['middleware'=> ['auth'], 'uses' => 'UserController@register']);

    Route::post('user/createUser', ['middleware'=> ['auth'], 'uses' => 'UserController@createUser']);


    
});
	
	





