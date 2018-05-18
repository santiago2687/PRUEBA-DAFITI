<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ListaPrecios extends Model
{
    protected $table = 'dlistapr0';

     /*Funcion que trae el stock de un producto por sku para luego actualizarlo en VTEX*/
    public static function getPriceBySku($sku) {
        if (!is_null($sku)) {
            $precio_lista = DB::table('dlistapr0')->select(['precio'])
            ->where('listapre', 'LIST_SUC')
            ->where('version', '3')
            ->where('codigo', $sku)->get();
            return $precio_lista;
        } 
    }   

}
