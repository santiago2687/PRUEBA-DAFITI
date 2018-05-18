<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\ApiServiceProvider;
use App\models\Pedidos;
use App\models\Stock;
use App\models\Tp_adic;
use App\models\E_Prod_Vtex_Activos;
use App\Http\Controllers\Api\Exception;

class ProductController extends Controller
{

	public function subirAllProducts() {
		/*Subo masivamente todos los productos a VTEX*/
		$all_active_products = E_Prod_Vtex_Activos::getAllProductosActivos();
		
		/*Valido si no hay ningun producto subido en VTEX*/
		if (count($all_active_products) == 0) {
			/*Llamo a la funcion para subir los productos*/
			$all_products = Tp_adic::getAllProducts();
			insertProducts($all_products);

		} else {
			/*Subir lote de productos cuando ya existen otros activos en la Plataforma de Pisano*/
		
			$list_sku_activos = array();

			foreach ($all_active_products as $key => $value) {
				$list_sku_activos[] = $value->sku;
			}

			/*Obtengo todos los productos que no estan activos en VTEX*/
			$all_products = tp_adic::getAllProductosNoActivos($list_sku_activos);
			insertProducts($all_products);

		}

	}

	/*Funcion para subir todo el catalogo de Productos, mas alla de que esten activos en VTEX*/
	public function forzarSubidaMasivaProductos() {
		/*Subis todos los productos aunque ya esten activos o no en VTEX*/
		$all_products = Tp_adic::getAllProducts();
		insertProducts($all_products);

	}

	/*Funcion para subir productos de Automotores a VTEX, mas alla de que esten activos en VTEX */
	public function forzarsubidaProductosAutomotor() {
		$all_products = tp_adic::getAllProductAutomotor("93");
		insertProducts($all_products);
	}

	public function forzarsubidaProductosSkuAuxiliar() {
		$all_products = tp_adic::getAllProductsAuxiliares();
		insertProducts($all_products);
	}

    public function productosSimilares() {
		productosSimilares();

	}

	public function actualizarAtributos() {

		/*Obtengo los productos ya activos*/
		$all_active_products = E_Prod_Vtex_Activos::getAllProductosActivos();

		/*Valido que haya productos activos*/
		if (count($all_active_products) > 0) {

			$list_sku_activos = array();

			foreach ($all_active_products as $key => $value) {
				$list_sku_activos[] = $value->sku;
			}
			
			$all_products = tp_adic::getAllProductosActivos($list_sku_activos);
			actualizarAtributos($all_products);

		}

	}

	public function skuSinImagenes() {

		$skus = Tp_adic::getAllSkus();
		$skus_sin_imagen = array();
		foreach ($skus as $key => $sku) {

			try{

			   	$url = file_get_contents("http://www.episano.com/uploads/productos/".$sku->codigo.".png");
	    	
			} catch(\Exception $e){
			  	//$skus_sin_imagen[] = array($sku->codigo);
			  	echo $sku->codigo."; \n";
			}

		}

	}


	/*Elimina todas las imagenes de un sku en particular y luego sube la imagen actual para ese sku en VTEXS*/
    public function actualizarImagen() {
    		
    	/*Obtengo los productos ya activos*/
		$all_active_products = E_Prod_Vtex_Activos::getAllProductosActivos();

		if (count($all_active_products) > 0) {

			foreach ($all_active_products as $key => $value) {
				/*Obtengo la url de la imagen del sku*/
				$product_imagen_link = Tp_adic::getImagenSKu($value->sku);
				$img_link = $product_imagen_link[0]->v_imagen;
				$img_name = $value->sku;
				borrarImagenesSku($value->sku);
				uploadProductImage($img_link, $img_name, $value->sku);
			}
	
    	}

	}

}
