<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class E_Prod_Vtex_Activos extends Model
{
    protected $table = 'e_prod_vtex_activos';

    public static function saveProductoActivo($sku = null) {

        DB::table('e_prod_vtex_activos')->insert([
            ['sku' => $sku, 'estado' => 'activo'],
        ]);

    	/*$item_activo = new E_Prod_Vtex_Activos();
        $item_activo->sku = (int)$sku;
        $item_activo->estado = "activo";
       	$item_activo->save();*/
           
    }

    public static function getAllProductosActivos() {
    	$allProductosActivos = DB::table('e_prod_vtex_activos')->select('sku')
                                ->Where('estado', 'activo')->get();
    	return $allProductosActivos;

    }

    public static function validarExistenciaSku($sku) {
        $product = DB::table('e_prod_vtex_activos')->select('sku')
                                ->Where('sku', $sku)->get();
        return $product;

    }

    public static function validarEstadoActivoSku($sku) {
        $product = DB::table('e_prod_vtex_activos')->select('sku')
                                ->Where('sku', $sku)
                                ->Where('estado', 'activo')->get();
        return $product;
    }

    public static function validarEstadoInactivoSku($sku) {
        $product = DB::table('e_prod_vtex_activos')->select('sku')
                                ->Where('sku', $sku)
                                ->Where('estado', 'inactivo')->get();
        return $product;

    }

    public static function actualizarEstadoSku($sku, $stado) {
        $insert = array(
                'estado' => $stado,
            );

        $result = DB::table('e_prod_vtex_activos')
                ->where('sku', $sku)
                ->update($insert);

    }

}
