<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Stock extends Model
{
    protected $table = 'stock0';

    /*Funcion que muestra el listado total de stock por productos*/
    public static function getStock() {
    	$allStock = DB::table('stock0')->select(['codigo', 'producto',  'detalle', 'costo', 's_deposito', 'stock'])->get();

        /*$allStock = DB::select(DB::raw("select stock0.codigo, stock0.producto, stock0.detalle, 
            stock0.costo, (stock0.s_deposito + stock0.stock) as stock
            from stock0"));*/

    	$allStock = collect($allStock);
        
    	return $allStock;
    }

    /*Funcion que trae el stock de un producto por sku para luego actualizarlo en VTEX*/
    public static function getStockBySku($sku) {
        if (!is_null($sku)) {

            /*$stock = DB::select(DB::raw("select (stock0.s_deposito + stock0.stock) as stock
            from stock0 where codigo = ".$sku));*/

            $stock = DB::table('stock0')->select(['s_deposito'])->where('codigo', $sku)->get();

            return $stock;

        } 
    } 

    /*Funcion que trae el stock de un producto por sku para luego actualizarlo en VTEX*/
    public static function getPriceBySku($sku) {
        if (!is_null($sku)) {
            $stock = DB::table('stock0')->select(['costo'])->where('codigo', $sku)->get();
            return $stock;
        } 
    }   
}
