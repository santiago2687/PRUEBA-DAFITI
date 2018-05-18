<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\ApiServiceProvider;
use App\models\Pedidos;
use App\models\Stock;
use App\models\Cliente;
use App\models\ItemPedido;
use App\models\E_Pedidos_Envio;
use MP;
use app\Mail;
use DB;

class PedidoController extends Controller
{
	/*funcion para obtener el listado de pedidos desde VTEX*/

    public function listarPedidos() {

    	//$mp = new MP("4707718906183497", "mPg72E47bIQU22wSz38rrtRAwg2b4pP2");
    	//var_dump($mp::get_payment('2672487996'));die;
    	/*$paymentInfo = MP::get_payment('2411924965');
    		
    	var_dump($paymentInfo['response']['collection']['status']);
    	var_dump(number_format($paymentInfo['response']['collection']['transaction_amount'], 2, '.', ''));die;*/

    	//\Log::info('Consultando los pedidos pendientes a VTEX');
    	/*llamamos a la funcion que se encarga de traer todos los pedidos que se hicieron en el e-commerce que esten en estado "ready_for_handling*/
    	$listadoPedidos = listaPedidos();
    	
    	$cant_pedidos = count($listadoPedidos->list);
    	
    	if ($cant_pedidos > 0) {

    		foreach ($listadoPedidos as $key => $value) {

    			$info_pedido = array();
	    		if ($key == "list") {

	    			foreach ($value as $k => $v) {

		    			$nro_pedido = $v->orderId;

		    			$info_pedido['norden'] = $nro_pedido;
		    			//\Log::info('Validando si existe el pedido');
		    			/*Valida si ya existe el pedido en la base de datos*/
			    		$result = Pedidos::validarExistenciaPedido($nro_pedido);

			    		if (count($result) == 0) {
			    			//\Log::info('Procesando pedido N° '.$nro_pedido);
			    			/*Se consulta a la api de VTEX para traer la informacion del pedido actual*/
						 	$detalle_pedido = $this->listarPedido($nro_pedido);
						 	$detalle_envio = get_object_vars($detalle_pedido->shippingData);
						 	
						 	//\Log::info('Validando si el cliente del pedido actual ya esta registrado');
						 	/*Se valida el cliente, si no existe... se agrega a la base de datos*/
						 	Cliente::validar_cliente(get_object_vars($detalle_pedido->clientProfileData), get_object_vars($detalle_pedido->shippingData));

						 	//\Log::info('Procesando informacion del pedido');
						 	/*Guardo la informacion necesaria del pedido en la Base de Datos*/
						 	$info_pedido['idcli'] = (float)$detalle_pedido->clientProfileData->document;
						 	$creation_date = date_create($detalle_pedido->creationDate);
						 	$info_pedido['fecha'] = date_format($creation_date,"Y-m-d H:i:s");

						 	$importe_total  = $detalle_pedido->value;

						 	/*Se formatea el importe total del pedido*/
						 	$importe_formateado = formatear_precio($importe_total);

						 	$info_pedido['importe'] = (float)$importe_formateado;

						 	$info_pedido['medio_pago'] = $detalle_pedido->paymentData->transactions[0]->payments[0]->paymentSystemName;

						 	/*Valido si la operacion viene por Mercado Pago o decidir*/
						 	if ($detalle_pedido->paymentData->transactions[0]->payments[0]->paymentSystemName == "Mercado Pago") {
						 		
								$id_trans_mp = $detalle_pedido->paymentData->transactions[0]->payments[0]->tid;

								/*Id de transaccion mercado pago*/
								$info_pedido['nro_mp'] = $id_trans_mp;

						 		$paymentInfo = MP::get_payment($id_trans_mp);
						 		
						 		$estado_mp = $paymentInfo['response']['collection']['status'];
						 		$importe_mp = number_format($paymentInfo['response']['collection']['transaction_amount'], 2, '.', '');
						 		
						 		
						 		/*cotejo datos con mercado pago, si son iguales se pone el estado de verificacion en 0 sino.. en 1*/
						 		if ($importe_mp == $importe_formateado && $estado_mp == "approved") {
						 			$info_pedido['verificacion_transaccion'] = 0;

						 		} else {
						 			$info_pedido['verificacion_transaccion'] = 1;
						 		}

						 	} elseif ($detalle_pedido->paymentData->transactions[0]->payments[0]->paymentSystemName != "Mercado Pago" && $detalle_pedido->paymentData->transactions[0]->payments[0]->paymentSystemName != "Cash") {
						 	
								$transactionId = $detalle_pedido->paymentData->transactions[0]->payments[0]->tid;
							
							        //$monto = $detalle_pedido->paymentData->transactions[0]->payments[0]->value;						

								if(property_exists($detalle_pedido->paymentData->transactions[0]->payments[0]->connectorResponses, "issuer")) {
								
									$medioPago = $detalle_pedido->paymentData->transactions[0]->payments[0]->paymentSystemName.'
									'.$detalle_pedido->paymentData->transactions[0]->payments[0]->connectorResponses->issuer;


								} else {
								
									$medioPago = $detalle_pedido->paymentData->transactions[0]->payments[0]->paymentSystemName;

								}

								$info_pedido['medio_pago'] = $medioPago;

								/*$res_val_decidir = validarDatosDecidirPedido($detalle_pedido->sequence, $detalle_pedido->paymentData->transactions[0]->payments[0]);*/

								//$info_pedido['nro_decidir'] = $res_val_decidir['nro_decidir'];
								//$info_pedido['verificacion_transaccion'] = $res_val_decidir['error'];
								$info_pedido['nro_decidir'] = "30B8FDABFA736CACEE1492706085013";
								$info_pedido['verificacion_transaccion'] = "0";

							}

						 	$info_pedido['cuotas'] = $detalle_pedido->paymentData->transactions[0]->payments[0]->installments;

						 	$valor_descuento = formatear_precio($detalle_pedido->totals[1]->value);

						 	$info_pedido['valor_desc'] = (float)$valor_descuento;

						 	$valor_envio = formatear_precio($detalle_pedido->totals[2]->value);

						 	$info_pedido['valor_envio'] = (float)$valor_envio;

						 	/*Guardamos en una variable el tipo de envío*/
						 	$tipo_envio = $detalle_pedido->shippingData->logisticsInfo[0]->deliveryIds[0]->courierId;
						 	
						 	switch ($tipo_envio) {
						 		case 'HASTA-EXPRESO':
						 		
						 			$info_pedido['tipo_envio'] = $tipo_envio;
						 			break;

						 		case 'ENVIO A DOMICILIO INTEGRAL PACK':
						 			$info_pedido['tipo_envio'] = $tipo_envio;
						 			break;

						 		case 'ENVIO_DOMICILIO_URBANO':
						 			/*quito el guion del nro de pedido para que Urbano pueda interpretar el tracking number*/
						 			$tracking_number = str_replace("-","",$v->orderId);
						 			
						 			$info_pedido['tipo_envio'] = $tipo_envio;
						 			$info_pedido['tracking_number'] = $tracking_number;
						 			break;

						 		default:
						 			$es_sucursal = strpos($tipo_envio, 'SUC');
						 	
								 	/*Validamos si el tipo de envío es una sucursal*/
								 	if ($es_sucursal === 0) {
								 		$info_pedido['tipo_envio'] = $tipo_envio;
								 		
								 	} else {
								 		$info_pedido['tipo_envio'] = $detalle_pedido->shippingData->logisticsInfo[0]->deliveryIds[0]->courierName;
								 		$info_peido['tracking_number'] = str_replace('-', "", $nro_pedido);

								 	}	
						 			break;
						 	}
						 	
							$info_pedido['estado'] = "ready-for-handling";
							
							//\Log::info('Guardando el pedido');
							/*Se guarda el pedido en la base de datos*/
							Pedidos::guardarPedido($info_pedido);

							/*Guardo la informacion de entrega del cliente relacionado al Pedido*/
							E_Pedidos_Envio::guardar_info_envio($detalle_pedido->clientProfileData, get_object_vars($detalle_pedido->shippingData), $nro_pedido);

							//\Log::info('Guardando los productos relacionados al pedido');
							/*Se guardan los items del pedido en la tabla e_items_pedido*/

							$items_pedido = $detalle_pedido->items;
							ItemPedido::saveItemByPedido($nro_pedido, $items_pedido);

							//\Log::info('Se finalizo el proceso de guardado para el pedido N° '.$nro_pedido);
														   
		    		 	} else {
		    		 		//\Log::info('Ya existe el pedido N° '.$nro_pedido);

		    		 	}

	    			}

	    		}
    	
    		}

    	} else {
    		\Log::info('No hay pedidos pendientes a procesar');

    	}

    	
    }

    public function listarPedido($orderId = null) {

		if(!is_null($orderId)) {
			$listar_pedido = pedido($orderId);
			
			return $listar_pedido;
			/*Luego se desarrolará la funcionalidad de esta función*/

		} else {
			die("no se a indicado el ID del pedido, por favor indique alguno");

		}
		
	}

	/*Busca los pedidos que estan con estado "cancelado" y "facturado" y actualiza la informacion en el e-commerce*/
	public function validarEstadoPedidos() {

		/*Buscar pedidos con estado "Cancelado y Facturado"*/
		//\Log::info('Buscando pedidos cancelados y Facturados...');
		$pedidos_cancelados_facturados = Pedidos::buscarPedidosCanceladosFacturados();
		$cant_pedidos_cancelados_facturados = count($pedidos_cancelados_facturados);

		if ($cant_pedidos_cancelados_facturados > 0 ) {

			//\Log::info('Procesando '.$cant_pedidos_cancelados_facturados.' pedidos cancelados');
			foreach ($pedidos_cancelados_facturados as $key => $value) {

				//\Log::info('Actualizando pedido N° '.$value->norden);
				$orderId = $value->norden;

				/*Validamos si el estado del pedido sea ready-for-handling*/
				$info_pedido = pedido($orderId);
				//\Log::info('el estado del pedido: '.$value->norden. ' es: '.$info_pedido->status);
			
				if ($info_pedido->status == 'ready-for-handling') {

					/*Actualizamos el estado del pedido en VTEX a start-handling*/
					$result = actualizarEstadoPedido($orderId, 'start-handling');
					
					/*obtiene los items de los pedidos para luego actualizar el stock de los mismos en VTEX*/
					$items_pedido = Pedidos::getInfoPedido($orderId);
					
					foreach ($info_pedido->items as $key) {
						
						$cantidad_actual = Stock::getStockBySku($key->refId);
						
						if ($value->estado == 'invoiced') {
							$cantidad_a_actualizar = (int)$cantidad_actual[0]->s_deposito;
							
						} else {
							$cantidad_a_actualizar = (int)$cantidad_actual[0]->s_deposito;
							
						}
						
						//\Log::info('Actualizando stock del producto código : '.$key->refId);
						
						/*actualiza el stock de un producto a la vez en VTEX*/

						$result = actualizarStockProducto($key->id, $cantidad_a_actualizar, 1);
						
						if ($result) {
							//\Log::info('Se actualizo correctamente el stock para el producto : '.$key->refId);
							
							/*Validamos si el estado del pedido sea start-handling*/
							$pedido_info = pedido($orderId);

							if ($pedido_info->status == 'handling') {	
								
								/*valido si el pedido en la DB esta como cancelado o facturado*/
								if ($value->estado == 'canceled') {
									/*Actualizar estado del pedido a "pedido de cancelamiento" en VTEX*/
									$result = actualizarEstadoPedido($orderId, 'cancel');
									//\Log::info('Se ha actualizado el estado del pedido N°'.$value->norden.' : Pedido de Cancelamiento');

									/*Validamos que el estado del pedido sea request-cancel en VTEX*/
									$pedido_info = pedido($orderId);
									
									if ($pedido_info->status == 'cancellation-requested') {	
										/*Actualizar estado del pedido a "canceled" en VTEX*/
										$result = actualizarEstadoPedido($orderId, 'cancel');

										/*Validamos si se actualizo correctamente a "cancelado" la orden y se marca como procesado el pedido en la DB*/
										$validacion_estado_pedido = pedido($orderId);
										if ($validacion_estado_pedido->status == 'canceled') {
											Pedidos::actualizarProcesamientoPedido($orderId);
											//\Log::info('Se ha actualizado el estado del pedido N°'.$value->norden.' : Cancelado');
										}

									}

								/*Valido que el estado del pedido en la db sea "invoiced*/
								} elseif ($value->estado == 'invoiced') {
									
									/*Busco la info del pedido en la base de datos*/
									$items_pedidos = Pedidos::getInfoPedidoItems($orderId);
				
									foreach ($items_pedidos as $ip => $item) {
										$array_items[$ip]['id'] = $item->sku;
										/*Formato de precio: 1000.00 => 100000*/
										$precio_formateado = str_replace(".", "", $item->precio);

										$array_items[$ip]['price'] = (float)$precio_formateado;
										$array_items[$ip]['quantity'] = (float)$item->cantidad;

									}

									//ENVIO A DOMICILIO URBANO

									/*Formato de factura:NFe-950 o A001 por ejemplo*/
                                    $array_facturacion = array();
									$array_facturacion['invoiceNumber'] = $value->nro_factura;
									/*Formato de importe: 1005.00 => 100500*/
									$array_facturacion['invoiceValue'] = $info_pedido->value;
									$array_facturacion['issuanceDate'] = $value->fecha_facturacion;
									$array_facturacion['invoiceUrl'] = "";
									$array_facturacion['trackingNumber'] = "";

									/*Valido si el pedido se hizo por Urbano y directamente cuando envio la facturacion a VTEX tambien envio la info del tracking del producto*/
									if ($value->tipo_envio == "ENVIO A DOMICILIO URBANO" || $value->tipo_envio == "ENVIO_DOMICILIO_URBANO") {
										//Desarrollo:   http://desarrollo.urbano.com.ar/webservice/cespecifica/?shi_codigo=671&cli_codigo=ANAHER0281
										//Producción: https://wbs.urbano.com.ar/cespecifica/?shi_codigo=671&cli_codigo=BUE181703

										//$array_facturacion['trackingNumber'] = $value->tracking_number;
										//$array_facturacion['trackingUrl'] = " http://desarrollo.urbano.com.ar/webservice/cespecifica/?shi_codigo=2069&cli_codigo=".$orderId;

										//$array_facturacion['invoiceUrl'] = "";
										//$array_facturacion['trackingNumber'] = "";
										
									} else {
										$array_facturacion['invoiceUrl'] = "";
										$array_facturacion['trackingNumber'] = "";

									}
									
									
									$array_facturacion['type'] = "Output";
									$array_facturacion['items'] = $array_items;

									$array_facturacion = json_encode($array_facturacion);
									/*Actualizo el pedido a Facturado en VTEX y envio la informacion a VTEX*/
							
									$result = actualizarEstadoPedido($orderId, 'invoice',$array_facturacion);
								
									/*Validamos si se actualizo correctamente a "facturado" la orden y se marca como procesado el pedido en la DB*/
									
									Pedidos::actualizarProcesamientoPedido($orderId);
									//\Log::info('Se ha actualizado el estado del pedido N°'.$value->norden.' : Facturado');
								}

							}

						} else {
							//\Log::info('no se pudo actualizar correctamente el stock para el producto : '.$key->sku);

						
                        }
					}

				} else {
					//\Log::info('No se puede actualizar el estado porque esta en estado "'.$info_pedido->status.'"');

				}
				
			} 
			
		} else { 
			//\Log::info('No hay pedidos cancelados y facturados pendientes a procesar');
		}
	}

	/*Funcion que envía los datos de pedido cuando el pedido es por medio de Integral Pack y no tiene todavia Tracking Number designado */
	public function envioPedidosIntegral() {

		enviarDatosEnvioPedidoIntegral();

	}


	/*Busca los tracking number de los pedidos que ya fueron enviados a Interal*/
	public function buscarTrackingNumberPedidosIntegral() {
		$pedidos = Pedidos::obtenerTrackingNumberPedidosIntegral();

		if (count($pedidos) > 0) {
			traerTrackingNumberPedidoIntegral($pedidos);

		}

	}

	/*Busca los tracking number de los pedidos que ya fueron enviados a Interal*/
	public function buscarTrackingNumberPedidosUrbano() {
		$pedidos = Pedidos::obtenerTrackingNumberPedidosUrbano();

		if (count($pedidos) > 0) {
			traerTrackingNumberPedidoUrbano($pedidos);

		}

	}

	/*Proceso que da aviso por medio de mail al cliente que tiene el pedido para retirar en sucursal*/
	public function envioMailAvisoRetiroSucursal() {
		$pedidos = Pedidos::obtenerInfoPedidoAvisoRetiro();
		
		$data =  array();
		foreach ($pedidos as $key => $pedido) { 
			
            $items_pedido = DB::table('e_pedido_item')
                            ->select('e_pedido_item.sku', 'e_pedido_item.precio', 'tp_adic0.detalle', 'tp_adic0.nombrevtx')
                            ->join('tp_adic0', 'tp_adic0.codigo', '=', 'e_pedido_item.sku')
                            ->where('e_pedido_item.orden_id', $pedido->norden)  
                            ->get();

            

            $info_sucursal = DB::table('empresas0')
                            ->select('empresas0.nombre', 'empresas0.email', 'empresas0.domicilio', 'empresas0.telefono')
                            ->where('empresas0.numero', $pedido->empresa)
                            ->get();           

            $data['nombre_apellido_cliente'] = $pedido->nombre.' '.$pedido->apellido;
            $data['nombre_sucursal'] = $info_sucursal[0]->nombre;
            $data['domicilio_sucursal'] = $info_sucursal[0]->domicilio;
            $data['telefono_sucursal'] = $info_sucursal[0]->telefono;
            $data['email_sucursal'] = $info_sucursal[0]->email;

            
            $cant = 1;
            $sku_ant = '';
            foreach ($items_pedido as $key => $item_pedido) {

        		$data['productos'][$key]['nombre_producto'] = $item_pedido->detalle.' '.$item_pedido->nombrevtx;
            	$data['productos'][$key]['precio_producto'] = $item_pedido->precio;

            }

			\Mail::send('mails.aviso_retiro_sucursal', ['data' => $data] ,function ($message) use ($data) {
	    		$message->from(config('mail.from')['address'], config('mail.from')['name']);
	    		$message->subject('Su pedido ya llego a la sucursal!');
	    		$message->to($pedido->e_mail)->cc('sistemas@pisano.com.ar');
			});

			Pedidos::actualizar_envio_mail_retiro_sucursal($pedido->norden);
            
        }

		
	}

}
