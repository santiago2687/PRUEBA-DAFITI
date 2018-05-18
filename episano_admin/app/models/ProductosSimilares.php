<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProductosSimilares extends Model
{
    protected $table = 'e_prod_similares';

    public static function getAllProductosSimilares($sku = array()) {
    	
    	if (count($sku) > 0) {
    		$allProductosSimilares = DB::table('e_prod_similares')
                    ->whereIn('sku_origen', $sku)->get();
    		return $allProductosSimilares;

    	} else {
    		$allProductosSimilares = DB::table('e_prod_similares')->get();
    		return $allProductosSimilares;

    	}
    }
}
