<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\models\Act_Stock_Precio;
use App\models\E_Prod_Vtex_Activos;
use App\models\ListaPrecios;
use App\models\Tp_adic;
use Maatwebsite\Excel\Excel;

class PrecioController extends Controller
{

	/*Actualiza Precio y Stock de un producto en VTEX*/
	public function actualizar_precio_stock() {

		//\Log::info('Consultando datos a actualizar sobre productos...');

		/*Traigo de la tabla de actualizacion de productos los registros a actualizar*/
		/*Valido si trae algun registro, si no no hay nada, entonces termina la consulta*/
		$registros_actualizar = Act_Stock_Precio::consultar_registros_actualizar();
		
		if (count($registros_actualizar) > 0) {
			/*recorro los registros para actualizar*/
			foreach ($registros_actualizar as $key => $value) {

				/*traigo de VTEX la info del producto a actualizar para obtener el id de producto*/
					$info_producto =  consultar_producto_by_sku($value->sku);

					if ($info_producto != "SKU não encontrado") {
						
						$id_producto = $info_producto->ProductId;

						/*Valido la info a actualizar*/
						if (!is_null($value->stock)) {

							$stock = $value->stock;
							/*Actualizo el stock del producto*/
							actualizarStockProducto($value->sku, $stock, 0);
							//\Log::info('Se actualizo el Stock correctamente para el sku '.$value->sku);
						
						}

						if (!is_null($value->precio)) {

							$precio = $value->precio;
							$info_precio_producto = consultPriceListBySku($value->sku);

							if ($info_precio_producto != "Could not find a price-sheet for the sku ".$value->sku.".") {

								$id = $info_precio_producto[0]->id;
							
								$array_update_price['id'] = $id;
								$array_update_price['itemId'] = $value->sku;
								$array_update_price['salesChannel'] = "1";
								$array_update_price['price'] = $precio;
								$array_update_price['listPrice'] = $precio;
								//var_dump($array_update_price);die;
								$result = updatePriceListBySku($array_update_price);
								//\Log::info('Se actualizo el precio correctamente para el sku '.$value->sku);

							}

						}

						if (!is_null($value->descripcion)) {
							$descripcion = $value->descripcion;

						}

						$result = Act_Stock_Precio::eliminar_registro_actualizacion($value->id);

						if ($result) {
							//\Log::info('Se elimino correctamente el registro '.$value->id);
							
						} else {
							//\Log::info('No se pudo eliminar el registro '.$value->id);

						}


					}

			}

		} else {
			//\Log::info('No se encontraron registros para actualizar');

		}

	}

	public function validarPrecioPreseaVtex() {

		$listado_informe_precios = [];

		$listado_informe_precios[] = ['sku', 'precio_presea', 'precio_vtex', 'diferencia'];

		$lista_sku_activos_vtex = E_Prod_Vtex_Activos::getAllProductosActivos();

		//var_dump($lista_sku_activos_vtex);die;

		foreach ($lista_sku_activos_vtex as $key => $sku) {
			/*Consulto precio de sku en VTEX*/
			$info_precio_producto = consultPriceListBySku($sku->sku);

			if ($info_precio_producto != "Could not find a price-sheet for the sku ".$sku->sku.".") {
				//var_dump($sku->sku);
				/*Consulto precio sku en presea*/
				$precio_presea_sku = ListaPrecios::getPriceBySku($sku->sku);

				$sku = $sku->sku;
				$precio_actual_presea = $precio_presea_sku[0]->precio;
				$precio_actual_vtex = $info_precio_producto[0]->listPrice;

				if ($precio_actual_presea != $precio_actual_vtex) {
					$diferencia = "hay dif";

				} else {
					$diferencia = "";

				}

				$listado_informe_precios[] = array($sku, $precio_actual_presea, $precio_actual_vtex, $diferencia);

			}

		}

		// Generate and return the spreadsheet
    	\Maatwebsite\Excel\Facades\Excel::create('comparacion_precios_presea_vtex', function($excel) use ($listado_informe_precios) {
        // Set the spreadsheet title, creator, and description
        $excel->setTitle('comparacion_precios_presea_vtex');
        $excel->setCreator('Pisano')->setCompany('Pisano Pinturerias');
        $excel->setDescription('compara los precios de presea y vtex por sku');

        // Build the spreadsheet, passing in the payments array
        $excel->sheet('comparacion_precios_presea_vtex', function($sheet) use ($listado_informe_precios) {
            $sheet->fromArray($listado_informe_precios, null, 'A1', false, false);
        });

    	})->store('xlsx', public_path('/precios_presea_vtex/'));

	}


	/*Funcion que actualiza masivamente los precios de todos los productos cargados en VTEX*/
	public function subidaMasivaPrecios() {

		/*Traigo todos los sku activos en VTEX*/
		$all_active_products = E_Prod_Vtex_Activos::getAllProductosActivos();
	
		/*Valido si no hay ningun producto subido en VTEX*/
		if (count($all_active_products) > 0) {

			/*recorro los registros para actualizar*/
			foreach ($all_active_products as $key => $value) {
				/*sku del producto*/
					$sku = $value->sku;

				/*Consulto la lista de precios del producto a actualizra*/
				$info_lista_precio_sku = consultPriceListBySku($sku); 
				
				if ($info_lista_precio_sku != "Could not find a price-sheet for the sku ".$sku.".") {
					$lista_precio_id = $info_lista_precio_sku[0]->id;

					$resultado_precio = ListaPrecios::getPriceBySku($sku);
					/*Precio del producto*/
					$precio_sku = $resultado_precio[0]->precio;

					/*Armamos el array de datos necesarios para actualizar precio de un sku en VTEX*/
					$array_update_price['id'] = $lista_precio_id;
					$array_update_price['itemId'] = $sku;
					$array_update_price['salesChannel'] = "1";
					$array_update_price['price'] = $precio_sku;
					$array_update_price['listPrice'] = $precio_sku;

					/*Sube el precio actualizado de un sku a VTEX*/
					updatePriceListBySku($array_update_price);

				} 

			}

		}

	}

}


