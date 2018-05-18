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
use Maatwebsite\Excel\Excel;

class InformesController extends Controller

{	

	public function _constructor() {
		$this->middleware('auth');

	}

 	public function getIndex() {
    	return View::make('pages.informes.informes_index');
    }

    public function exportarSkuActivos() {
    	$result_informe = Tp_adic::informe_productos_activos_vtex();
    	$informeArray = [];
    	$informeArray[] = ['sku', 'nombre de fabrica','marca','departamento','categoria', 'nombre mercado libre','stock', 'estado'];

    	foreach ($result_informe as $row) {
        	$informeArray[] = (array)$row;
    	}

    	// Generate and return the spreadsheet
    	\Maatwebsite\Excel\Facades\Excel::create('Skus Activos VTEX', function($excel) use ($informeArray) {
        // Set the spreadsheet title, creator, and description
        $excel->setTitle('Skus Activos VTEX');
        $excel->setCreator('Pisano')->setCompany('Pisano Pinturerias');
        $excel->setDescription('skus que estan subidos al catalogo del e-commerce');

        // Build the spreadsheet, passing in the payments array
        $excel->sheet('Skus Activos VTEX', function($sheet) use ($informeArray) {
            $sheet->fromArray($informeArray, null, 'A1', false, false);
        });

    	})->download('xlsx');

    }

    public function exportarTpAdic() {

    	$result_informe = Tp_adic::informe_tp_adic();
    	$informeArray = [];
    	$informeArray[] = ['codigo', 'nombre_de_fabrica', 'marca', 'departamento', 'categoria', 'subcategoria', 'patron', 'nro_patron', 'color', 'base', 'superficie', 'exposicion', 'aplicacion', 'descripcion', 'nombre_mercado_libre', 'estado_vtex', 'estad_presea'];

    	                                 

    	foreach ($result_informe as $row) {
        	$informeArray[] = (array)$row;
    	}

    	// Generate and return the spreadsheet
    	\Maatwebsite\Excel\Facades\Excel::create('TP ADIC ACTUAL', function($excel) use ($informeArray) {
        // Set the spreadsheet title, creator, and description
        $excel->setTitle('TP ADIC ACTUAL');
        $excel->setCreator('Pisano')->setCompany('Pisano Pinturerias');
        $excel->setDescription('TP ADIC ACTUAL');

        // Build the spreadsheet, passing in the payments array
        $excel->sheet('TP ADIC ACTUAL', function($sheet) use ($informeArray) {
            $sheet->fromArray($informeArray, null, 'A1', false, false);
        });

    	})->download('xlsx');

    }

    public function exportarCompPreciosPreseaVtex() {

        return response()->download(public_path('/precios_presea_vtex/comparacion_precios_presea_vtex.xlsx'));

    }

    public function skuSinImagenes() {

        $skus = Tp_adic::getAllSkus();
        $skus_sin_imagen = array();
        $skus_sin_imagen[]  = ['sku_sin_imagen'];
        foreach ($skus as $key => $sku) {

            try{

                $url = file_get_contents("http://www.episano.com/uploads/productos/".$sku->codigo.".png");
            
            } catch(\Exception $e){
                $skus_sin_imagen[] = array($sku->codigo);
                //echo $sku->codigo."; \n";
            }

        }
        
        // Generate and return the spreadsheet
        \Maatwebsite\Excel\Facades\Excel::create('SKU SIN IMAGEN', function($excel) use ($skus_sin_imagen) {
        // Set the spreadsheet title, creator, and description
        $excel->setTitle('SKU SIN IMAGEN');
        $excel->setCreator('Pisano')->setCompany('Pisano Pinturerias');
        $excel->setDescription('SKU SIN IMAGEN');

        // Build the spreadsheet, passing in the payments array
        $excel->sheet('SKU SIN IMAGEN', function($sheet) use ($skus_sin_imagen) {
            $sheet->fromArray($skus_sin_imagen, null, 'A1', false, false);
        });

        })->download('xlsx');

    }

}
