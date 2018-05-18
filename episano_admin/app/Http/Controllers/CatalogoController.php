<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use View;
use Session;
use App\models\Tp_adic;
use App\models\E_Prod_Vtex_Activos;
use App\models\Stock;
use App\Http\Controllers\Input;
use Yajra\Datatables\Datatables;

class CatalogoController extends Controller
{
    public function _constructor() {
		$this->middleware('auth');

	}

	public function getIndex() {
		$cant_productos = Tp_adic::getCantProductos();
		return View::make('pages.catalogo.catalogo_index')->with('cant_productos', $cant_productos);

	}

	public function showCatalogoDataTable() {
		return Datatables::of(Tp_adic::get_all_product_catalogo())->make(true);

	}

	public function productoDetalle($sku = null) {
		if (!is_null($sku)) {
			$info_producto = Tp_adic::getDetalleProducto($sku);
			return View::make('pages.catalogo.catalogo_detalle')->with('info_producto', $info_producto);
		}

	}

	public function subirProductoBySKu($sku = null) {

		if(!is_null($sku)) {
			/*Validar si los sku ya existen en la tabla y estan activos en VTEX*/
			$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);

			if (count($producto_no_existente) > 0) {
				$info_product = Tp_adic::getProductBySku($sku);
				borrarImagenesSku($sku);
				insertProducts($info_product);
				return redirect('/catalogo');

			} else {
				/*Si no existe en la tabla de sku activos pero si existe el producto en el catalogo*/
				$sku_existente = Tp_adic::getProductBySku($sku);
				if (count($sku_existente) > 0) {
					insertProducts($sku_existente);
					return redirect('/catalogo');

				} else {
					echo "no se paso un sku válido como parametro";

				}
				
			}

		} else {
			echo "no se paso un sku como parametro";

		}

	}

	/*Subir y activar productos*/
	public function subirProductos(Request $request) {
		$sku_array = json_decode($request->input("skus"));

    	/*Valido si el listado de skus esta vacio*/
    	if (count($sku_array == 0)) {
    		/*Recorro los skus que quiero subir a VTEX*/
    		foreach ($sku_array as $key => $sku) {

    			/*Validar si los sku ya existen en la tabla y estan activos en VTEX*/
				//$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
				/*Validar si el sku existe en la tp_adic0*/
				$info_product = Tp_adic::getProductBySku($sku);
				if (count($info_product) > 0) {
					borrarImagenesSku($sku);
					insertProducts($info_product);
				
				} 

			}

			return "true";

		} else {
			return "no se paso un sku como parametro";

		}

    }

    /*Funcion para activar prooductos que estan inactivos en VTEX y ya existen*/
    public function activarProductoBySku($sku = null) {
    	
    	if (!is_null($sku)) {
    		/*Valido si existe el Sku en la tabla de productos_activos en VTEX*/
    		$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);

    		if (count($producto_no_existente)) {
    			/*Valido si el producto esta activo o inactivo en la base de datos*/
    			$producto_activo = E_Prod_Vtex_Activos::validarEstadoInactivoSku($sku);
    			/*Si el producto esta inactivo*/
    			if (!is_null($producto_activo)) {
    				activeProduct($sku);
    				E_Prod_Vtex_Activos::actualizarEstadoSku($sku, 'activo');
    				return redirect('/catalogo');

    			} else {
    				echo "El producto ya se encuentra activo en VTEX";

    			}
    				
    		}
    		
    	} else {
    		echo "No existe el Sku que se quiere activar";

    	}
    	

    }


    public function activarProductos(Request $request) {
    	$sku_array = json_decode($request->input("skus"));

    	/*Valid que el array no este vacio*/
    	if (count($sku_array) > 0) {

    		/*Recorro los skus que voy a activar en VTEX*/
    		foreach ($sku_array as $key => $sku) {

	    		/*Valido si existe el Sku en la tabla de productos_activos en VTEX*/
	    		$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);

	    		if (count($producto_no_existente)) {
	    			/*Valido si el producto esta activo o inactivo en la base de datos*/
	    			$producto_activo = E_Prod_Vtex_Activos::validarEstadoInactivoSku($sku);
	    			/*Si el producto esta inactivo*/
	    			if (!is_null($producto_activo)) {
	    				activeProduct($sku);
	    				E_Prod_Vtex_Activos::actualizarEstadoSku($sku, 'activo');
	    			
	    			} else {
	    				return "El producto ya se encuentra activo en VTEX";

	    			}
	    				
	    		}
    			
    		}

    		return "true";

    	} else {
    		return "No existe el Sku que se quiere activar";

    	}
    	

    }


    public function inactivarProductoBySku($sku = null) {
    	if (!is_null($sku)) {
    		/*Valido si existe el Sku en la tabla de productos_activos en VTEX*/
    		$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
    		if (count($producto_no_existente)) {
    			/*Valido si el producto esta activo o inactivo en la base de datos*/
    			$producto_inactivo = E_Prod_Vtex_Activos::validarEstadoActivoSku($sku);
    			if (!is_null($producto_inactivo)) {
    				inactiveProduct($sku);
    				E_Prod_Vtex_Activos::actualizarEstadoSku($sku, 'inactivo');
    				return redirect('/catalogo');

    			} else {
    				echo "El producto ya se encuentra inactivo en VTEX";

    			}
    				
    		}
    		
    	} else {
    		echo "No existe el Sku que se quiere activar";

    	}

    }

    public function inactivarProductos(Request $request) {
    	$sku_array = json_decode($request->input("skus"));
    	
    	/*valido que el array no venga vacio*/
    	if (count($sku_array) > 0) {
			
			/*Recorro los skus para desactivar en VTEX*/
			foreach ($sku_array as $key => $sku) {

				/*Valido si existe el Sku en la tabla de productos_activos en VTEX*/
	    		$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
	    		if (count($producto_no_existente)) {
	    			/*Valido si el producto esta activo o inactivo en la base de datos*/
	    			$producto_inactivo = E_Prod_Vtex_Activos::validarEstadoActivoSku($sku);
	    			if (!is_null($producto_inactivo)) {
	    				inactiveProduct($sku);
	    				E_Prod_Vtex_Activos::actualizarEstadoSku($sku, 'inactivo');
	    		
	    			} else {
	    				echo "El producto ya se encuentra inactivo en VTEX";

	    			}
	    				
	    		}

			}

			return "true";

    	} else {
    		echo "No existe el Sku que se quiere activar";

    	}

    }

    public function actualizarAtributos($sku = null) {
    	if (!is_null($sku)) {

    		/*Validar si los sku ya existen en la tabla y estan activos en VTEX*/
			$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
			if (count($producto_no_existente) > 0) {
				$info_product = Tp_adic::getProductBySku($sku);
				actualizarAtributos($info_product);
				return redirect('/catalogo');
			
			} else {
				echo "El producto no se encuentra en VTEX";

			}

    	} else {
			return "no se paso un sku como parametro";

		}

    }

    public function actAtributos(Request $request) {
    	$sku_array = json_decode($request->input("skus"));

    	/*Valido si el listado de skus esta vacio*/
    	if (count($sku_array == 0)) {
    		/*Recorro los skus de los que quiero actualizar los atributos en VTEX*/
    		foreach ($sku_array as $key => $sku) {

    			/*Validar si los sku ya existen en la tabla y estan activos en VTEX*/
				$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
				if (count($producto_no_existente) > 0) {
					$info_product = Tp_adic::getProductBySku($sku);
					actualizarAtributos($info_product);
				
				} 

			}

			return "true";

		} else {
			return "no se paso un sku como parametro";

		}

    }

    /*Elimina todas las imagenes de un sku en particular y luego sube la imagen actual para ese sku en VTEXS*/
    public function actualizarImagen($sku = null) {
    	if (!is_null($sku)) {

    		/*Validar si los sku ya existen en la tabla y estan activos en VTEX*/
			$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
			if (count($producto_no_existente) > 0) {
				/*Obtengo la url de la imagen del sku*/
				$product_imagen_link = Tp_adic::getImagenSKu($sku);
				$img_link = $product_imagen_link[0]->v_imagen;
				$img_name = $sku;
				borrarImagenesSku($sku);
				uploadProductImage($img_link, $img_name, $sku);
				return redirect('/catalogo');
			
			} else {
				echo "El producto no se encuentra en VTEX";

			}

    	} else {
			return "no se paso un sku como parametro";

		}
    		
    }

    /*Elimina y sube imagenes en VTEX por un grupo de skus via AJAX*/
    public function actImagenes(Request $request) {

    	$sku_array = json_decode($request->input("skus"));
    	
    	/*Valido si el listado de skus esta vacio*/
    	if (count($sku_array == 0)) {
    		/*Recorro los skus de los que quiero actualizar los atributos en VTEX*/
    		foreach ($sku_array as $key => $sku) {
    			
    			/*Validar si los sku ya existen en la tabla y estan activos en VTEX*/
				$producto_no_existente = E_Prod_Vtex_Activos::validarExistenciaSku($sku);
				if (count($producto_no_existente) > 0) {
					/*Obtengo la url de la imagen del sku*/
					$product_imagen_link = Tp_adic::getImagenSKu($sku);
					$img_link = $product_imagen_link[0]->v_imagen;
					$img_name = $sku;
					borrarImagenesSku($sku);
					uploadProductImage($img_link, $img_name, $sku);
				} 

			}

			return "true";

		} else {
			return "no se paso un sku como parametro";

		}

  	}

}
