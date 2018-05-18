<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use App\models\E_Prod_Vtex_Activos;
use DB;


class Act_Stock_Precio extends Model
{	

	protected $table = 'e_act_stock_precio';

    //obtiene de la tabla los registros con los datos a actualizar por producto
    public static function consultar_registros_actualizar() {

    	$registros_actualizar = DB::table('e_act_stock_precio')
        ->Select('e_act_stock_precio.id', 'e_act_stock_precio.sku', 'e_act_stock_precio.stock', 'e_act_stock_precio.precio', 'e_act_stock_precio.descripcion')
        ->join('e_prod_vtex_activos', 'e_prod_vtex_activos.sku', '=', 'e_act_stock_precio.sku')
        ->Where('e_prod_vtex_activos.estado', 'activo')
        ->orderBy('e_act_stock_precio.sku','asc')
        ->get();
        return $registros_actualizar;

    }

    public static function eliminar_registro_actualizacion($id_registro = null) {
    	if (!is_null($id_registro)) {
    		$act_stock_precio = Act_Stock_Precio::find($id_registro);
            if (!is_null($act_stock_precio)) {
                $result = $act_stock_precio->delete();
                return $result;

            }
    		
    	}
    }
}
