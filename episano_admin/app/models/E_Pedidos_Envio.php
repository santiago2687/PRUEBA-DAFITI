<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class E_Pedidos_Envio extends Model
{
    protected $table = 'e_pedidos_envio';

        /*funcion que valida la existencia del cliente a traves de los pedidos. Si no existe, se da de alta*/
    public static function guardar_info_envio ($cliente_profile_info, $cliente_shipping_info, $id_pedido) {
    	
    	
    	if(!is_null($id_pedido)) {
    		
	        /*Valido que la informacion del cliente no este vacia*/
	    	if (count($cliente_profile_info) > 0 && count($cliente_shipping_info) > 0 ) {
	    		$info_pedido_envio = new E_Pedidos_Envio();

	    		$existe_datos_pedido = $info_pedido_envio::validar_existencia_datos_envio_pedido($id_pedido);

	    		/*Valido si ya esta cargado los datos de envio del pedido*/
	    		if (count($existe_datos_pedido == 0)) {

	    			/*desencripto el email del cliente*/
		    		$email = desencriptar_email($cliente_profile_info->email);
		    		$info_pedido_envio->nro_orden = $id_pedido;
		    		$info_pedido_envio->id_cliente = $cliente_profile_info->document;
		    		$info_pedido_envio->nombre = $cliente_profile_info->firstName;
		    		$info_pedido_envio->apellido = $cliente_profile_info->lastName;
		    		$info_pedido_envio->dni = $cliente_profile_info->document;
			      	$info_pedido_envio->telefono = $cliente_profile_info->phone;
			      	$info_pedido_envio->e_mail = $email->email;

			      	if (array_key_exists('complement', $cliente_shipping_info['address'])) {
						 $detalle_envio_complemento = $cliente_shipping_info['address']->complement;
					} else {
						$detalle_envio_complemento = '';
					}

			      	$info_pedido_envio->domicilio = $cliente_shipping_info['address']->street." ".$cliente_shipping_info['address']->number." ".$detalle_envio_complemento;
			      	$info_pedido_envio->ciudad = $cliente_shipping_info['address']->city;
			      	$info_pedido_envio->c_postal = $cliente_shipping_info['address']->postalCode;
			      	$info_pedido_envio->provincia = $cliente_shipping_info['address']->state;
			      	$info_pedido_envio->pais = $cliente_shipping_info['address']->country;
		    		$info_pedido_envio->save();
	    			
	    		}
	    	
	    	} 

    	}


    }

    public static function validar_existencia_datos_envio_pedido($id_pedido) {
    	$info_pedido = DB::table('e_pedidos_envio')
        ->select('e_pedidos_envio.nro_orden')
        ->where('e_pedidos_envio.nro_orden', $id_pedido)
        ->get();
        return $info_pedido;

    }

}
