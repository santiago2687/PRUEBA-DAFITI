<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    protected $table = 'e_pedido_item';

    /*Funcion que guarda los items de un pedido traido desde VTEX*/
    public static function saveItemByPedido($orderId, $info_item = array()) {
    	if (!is_null($info_item) && is_array($info_item) && !is_null($orderId)) {
            foreach ($info_item as $key) {
                $item_pedido = new ItemPedido();
                if (!is_null($key->refId)) {
                    $item_pedido->sku = $key->refId;
                    $item_pedido->cantidad = $key->quantity;
                    $item_pedido->precio = formatear_precio($key->price);
                    $item_pedido->wharehouseId = "1_1";
                    $item_pedido->orden_id = $orderId;
                    $item_pedido->save();
                } else {
                    \Log::info('El item no posee SKU');
                } 
                
            }
            
    	} else {

    	}
    }

    /*Obtiene todos los items relacionados a un pedido*/
    public static function getAllItemsByPedido($orderId) {
    	if (!is_null($orderId)) {


    	} else {

    	}
    } 

    /*Obtiene un item en particular relacionado a un pedido*/
    public static function getItemByPedido($orderId) {
    	if (!is_null($orderId)) {


    	} else {

    	}
    } 
}
