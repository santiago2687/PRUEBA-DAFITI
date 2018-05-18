<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Cliente extends Model
{
    protected $table = "e_clientes";

    /*funcion que valida la existencia del cliente a traves de los pedidos. Si no existe, se da de alta*/
    public static function validar_cliente ($cliente_profile_info, $cliente_shipping_info) {
    	/*Valido si ya existe el cliente en la base de datos, si no existe lo crea*/
    	$cliente_info = DB::table('e_clientes')->where('dni', $cliente_profile_info['document'])
        ->get();
        
    	if (count($cliente_info) === 0 ) {
    		$cliente = new Cliente();
    		
    		/*desencripto el email del cliente*/
    		$email = desencriptar_email($cliente_profile_info['email']);
    		$cliente->nombre = $cliente_profile_info['firstName'];
    		$cliente->apellido = $cliente_profile_info['lastName'];
	      	$cliente->dni = $cliente_profile_info['document'];
	      	$cliente->telefono = $cliente_profile_info['phone'];
	      	$cliente->e_mail = $email->email;
	      	$cliente->domicilio	= $cliente_shipping_info['address']->street." ".$cliente_shipping_info['address']->number;
	      	$cliente->ciudad = $cliente_shipping_info['address']->city;
	      	$cliente->c_postal = $cliente_shipping_info['address']->postalCode;
	      	$cliente->provincia	= $cliente_shipping_info['address']->state;
	      	$cliente->pais = $cliente_shipping_info['address']->country;

    		$cliente->save();

    	} 
    }
}
