<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\models\Stock;
use Illuminate\Support\ApiServiceProvider;

class StockController extends Controller
{
    public function actualizacionStock () {

    	/*Traemos todo el stock de todos los productos*/
    	//\Log::info('Consultando todo el stock de la base de datos');
    	$allStock = Stock::getStock();
        
    	/*recorro el stock y por sku consulto y comparo el stock con VTEX para luego decidir si actualizar o no*/
    	foreach ($allStock as $key => $stock) {
    		$sku = $stock->codigo;
    		/*quito los decimales del stock ya que siempre son numeros enteros*/
    		$stock = ceil($stock->s_deposito);
    		//\Log::info('Stock en la DB para el producto: '.$sku);

    		/*Consulto el estado actual del stock en VTEX por sku*/
    		$stock_vtex = json_decode(consultarStockBySku($sku));
    		//\Log::info('Stock actual en VTEX para el producto: '.$sku);

    		/*Comparando stock entre VTEX y DB*/
    		//\Log::info('Comparando stock entre VTEX y DB...');
    		if ($stock != $stock_vtex->balance[0]->totalQuantity) {
    			//\Log::info('Actualizando stock en VTEX para el producto: '.$sku);
            
    		  $result = actualizarStockProducto($sku, $stock, 0);
    			//\Log::info('Se ha actualizado el stock del producto: '.$sku.' en '.$stock);

    		} else {
    			//\Log::info('El stock es el mismo para el producto: '.$sku);
    		}
    		
    	}

    }
}
