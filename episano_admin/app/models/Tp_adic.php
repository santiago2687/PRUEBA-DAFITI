<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\models\ListaPrecios;
use App\models\E_Prod_Vtex_Activos;

class Tp_adic extends Model
{
    protected $table = 'tp_adic0';

    public static function getAllProducts() {

    	$allSProducts = DB::table('tp_adic0')->get();
    	return $allSProducts;

    }

    public static function getProductBySku($sku) {
        $product = DB::table('tp_adic0')->Where('codigo', $sku)->get();
        return $product;

    }

    public static function getProductIdBySKu($sku) {
        $id_product = DB::table('tp_adic0')->Select('v_id_produ')->Where('codigo', $sku)->get();
        return $id_product;
    }

    public static function getAllProductAutomotor($id_dep) {
        $products = DB::table('tp_adic0')->Where('v_id_dep', $id_dep)->get();
        return $products;
    }

    public static function getAllProductsAuxiliares() {
        $products = DB::table('tp_adic0')->WhereIn('codigo', [20003])->get();
        return $products;
    }

    public static function getAllProductosNoActivos($list_sku = array()) {
    	
    	$allProductosNoActivos = DB::table('tp_adic0')
                    ->whereNotIn('codigo', $list_sku)->get();
    	return $allProductosNoActivos;

    }

    public static function getAllProductosActivos($list_sku = array()) {
        $allProductosActivos = DB::table('tp_adic0')
                    ->whereIn('codigo', $list_sku)->get();
        return $allProductosActivos;

    }

    public static function getImagenSKu($sku) {
        $imagen_product = DB::table('tp_adic0')->Select('v_imagen')->Where('codigo', $sku)->get();
        return $imagen_product;
    }

    public static function get_all_product_catalogo() {
    	/*$catalogo = DB::table('tp_adic_test')
    	->select('tp_adic_test.codigo', 'tp_adic_test.v_nomvtex', 'tp_adic_test.marca', 'tp_adic_test.v_departam', round('stock0.costo'), 'stock0.stock', 'stock0.s_deposito')
    	->Leftjoin('stock0', 'stock0.codigo', '=', 'tp_adic_test.codigo')
    	->get();*/

        $catalogo = DB::select(DB::raw("select tp_adic0.codigo, tp_adic0.nombrevtx, tp_adic0.marca, 
            tp_adic0.v_departam, round(dlistapr0.precio, 2) as precio, stock0.stock, stock0.s_deposito, tp_adic0.producto1, e_prod_vtex_activos.estado, tp_adic0.superficie, tp_adic0.detalle, tp_adic0.producto1 
            from tp_adic0
            left join dlistapr0 on dlistapr0.codigo = tp_adic0.codigo
            left join stock0 on stock0.codigo = tp_adic0.codigo
            left join e_prod_vtex_activos on  e_prod_vtex_activos.sku = tp_adic0.codigo
            where dlistapr0.listapre = 'LIST_SUC'
            and dlistapr0.version = 3"));

    	$catalogo = collect($catalogo);

    	return $catalogo;
    }

    public static function get_all_product_catalogo_ventas() {
        /*$catalogo = DB::table('tp_adic_test')
        ->select('tp_adic_test.codigo', 'tp_adic_test.v_nomvtex', 'tp_adic_test.marca', 'tp_adic_test.v_departam', round('stock0.costo'), 'stock0.stock', 'stock0.s_deposito')
        ->Leftjoin('stock0', 'stock0.codigo', '=', 'tp_adic_test.codigo')
        ->get();*/

        $catalogo = DB::select(DB::raw("select tp_adic0.codigo, tp_adic0.nombrevtx, tp_adic0.marca, 
            tp_adic0.v_departam, round(dlistapr0.precio, 2) as precio, stock0.stock, stock0.s_deposito, tp_adic0.producto1, tp_adic0.color, tp_adic0.base, stock0.estado, tp_adic0.detalle
            from tp_adic0 
            left join dlistapr0 on dlistapr0.codigo = tp_adic0.codigo
            left join stock0 on stock0.codigo = tp_adic0.codigo
            where dlistapr0.listapre = 'LIST_SUC' 
            and dlistapr0.version = 3
            and stock0.estado != 'D'"));

        $catalogo = collect($catalogo);

        return $catalogo;
    }

    public static function getCantProductos() {
        $count = Tp_adic::count();
        return $count;
    }

    public static function getDetalleProducto($sku) {
        $catalogo = DB::table('tp_adic0')
        ->select('tp_adic0.nombrevtx', 'tp_adic0.detalle', 'tp_adic0.descripcio', 'tp_adic0.marca', 'tp_adic0.v_departam', 'tp_adic0.patron', 'tp_adic0.v_categor', 'tp_adic0.v_imagen','stock0.costo', 'tp_adic0.producto1',  'dlistapr0.precio')
        ->Leftjoin('stock0', 'stock0.codigo', '=', 'tp_adic0.codigo')
        ->Leftjoin('dlistapr0', 'dlistapr0.codigo', '=', 'tp_adic0.codigo')
        ->Where('tp_adic0.codigo', $sku)
        ->Where('dlistapr0.listapre', 'LIST_ECOM')
        ->Where('dlistapr0.version', '1')
        ->get();

        $catalogo = collect($catalogo);

        return $catalogo;

    }

    public static function getDetalleProductoVentas($sku) {
        $catalogo = DB::table('tp_adic0')
        ->select('tp_adic0.nombrevtx', 'tp_adic0.descripcio', 'tp_adic0.marca', 'tp_adic0.v_departam', 'tp_adic0.patron', 'tp_adic0.v_categor', 'tp_adic0.v_imagen','stock0.costo', 'tp_adic0.producto1',  'dlistapr0.precio', 'tp_adic0.color', 'tp_adic0.base')
        ->Leftjoin('stock0', 'stock0.codigo', '=', 'tp_adic0.codigo')
        ->Leftjoin('dlistapr0', 'dlistapr0.codigo', '=', 'tp_adic0.codigo')
        ->Where('tp_adic0.codigo', $sku)
        ->Where('dlistapr0.listapre', 'LIST_SUC')
        ->Where('dlistapr0.version', '3')
        ->get();

        $catalogo = collect($catalogo);

        return $catalogo;

    }

    public static function getAllSkus() {

        $info_pedido = DB::table('tp_adic0')->select('tp_adic0.codigo')->get();
        
        return $info_pedido;
    }

    public static function informe_productos_activos_vtex() {

        $informe = DB::table('tp_adic0')->select('tp_adic0.codigo as sku', 'tp_adic0.detalle as nombre de fabrica', 'tp_adic0.marca', 'tp_adic0.producto1 as departamento', 'tp_adic0.v_categor as categoria', 'tp_adic0.nombreml as nombre_ml', 'stock0.s_deposito as stock', 'e_prod_vtex_activos.estado as estado')
        ->join('e_prod_vtex_activos', 'e_prod_vtex_activos.sku', '=', 'tp_adic0.codigo')
        ->join('stock0', 'stock0.codigo', '=', 'tp_adic0.codigo')
        ->where('e_prod_vtex_activos.estado', 'activo')
        ->orderBy('tp_adic0.codigo', 'asc')
        ->get();

        $informe = collect($informe);

        return $informe;

    }

    public static function informe_tp_adic() {
        $tp_adic = DB::table('tp_adic0')->select('tp_adic0.codigo', 'tp_adic0.detalle as nombre_de_fabrica', 'tp_adic0.marca',  'tp_adic0.v_departam as departamento', 'tp_adic0.v_categor as categoria', 'tp_adic0.v_subcate as subcategoria', 'tp_adic0.patron', 'tp_adic0.v_n_patron as nro_patron', 'tp_adic0.color', 'tp_adic0.base', 'tp_adic0.superficie', 'tp_adic0.sup1 as exposicion', 'tp_adic0.aplicacion', 'tp_adic0.descripcio as descripcion', 'tp_adic0.nombreml as nombre_mercado_libre', 'e_prod_vtex_activos.estado', 'stock0.estado as estado_presea')
        //->Leftjoin('dlistapr0', 'dlistapr0.codigo', '=', 'tp_adic0.codigo')
        ->Leftjoin('e_prod_vtex_activos', 'e_prod_vtex_activos.sku', '=', 'tp_adic0.codigo')
        ->Leftjoin('stock0', 'stock0.codigo', '=', 'tp_adic0.codigo')
        //->Where('dlistapr0.listapre', 'LIST_ECOM')
        //->Where('dlistapr0.version', '1')
        ->orderBy('tp_adic0.codigo', 'asc')
        ->get();

        $tp_adic = collect($tp_adic);

        return $tp_adic;

    }

}
