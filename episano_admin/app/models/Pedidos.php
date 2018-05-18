<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;


class Pedidos extends Model
{
    protected $table = 'e_pedidos';


    /*Muestra todos los pedidos de la base de datos que hayan pasado 30 minutos despues de su creacion en VTEX*/
    public static function mostrarPedidos() {
    	$allPedidos = DB::table('e_pedidos')->select(['id', 'norden', 'idcli', 'importe', 
            'fecha', 'estado', 'tipo_envio', 'nro_factura', 'nro_presupuesto', 'nro_mp', 
            'nro_decidir', 'verificacion_transaccion', 'pedido_cancelacion', 'resultado_cancelacion'])->get();
    	//->whereRaw('TIMESTAMPDIFF(MINUTE, fecha, now()) >= 30')
    
    	$allPedidos = collect($allPedidos);

    	return $allPedidos;

    }

    public static function getCantPedidos() {
        //$allPedidos = DB::table('e_pedidos')->select(['id', 'norden', 'idcli', 'importe', 'fecha', 'estado', 'nro_factura', 'nro_presupuesto'])->get();
        $count = Pedidos::count();
        return $count;
    }

    public static function getInfoPedido($orderId) {
    	$info_pedido = DB::table('e_pedidos')
        ->select('e_pedidos.*', 'e_pedido_item.*', 'stock0.detalle', 'stock0.s_deposito', 'e_pedidos_envio.*')
        ->join('e_pedido_item', 'e_pedido_item.orden_id', '=', 'e_pedidos.norden')
        ->join('stock0', 'stock0.codigo', '=', 'e_pedido_item.sku')
        ->join('e_pedidos_envio', 'e_pedidos_envio.nro_orden', '=', 'e_pedidos.norden')
        ->where('norden', $orderId)
        ->get();
        
    	return $info_pedido;

    }

    public static function validarCantidadPedidos($cant = null) {
    	if (!is_null($cant)) {
    		$cantidad_actual = Pedidos::count();

    		if($cantidad_actual > $cant) {
    			return true;

    		} else {
    			return $cant;

    		}
    	}
    }

    public static function validarExistenciaPedido($nro_pedido) {
    	$info_pedido = DB::table('e_pedidos')->where('norden', $nro_pedido)->get();

    	return $info_pedido;

    }

    public static function guardarPedido($info_pedido = array()) {
       
    	$pedido = new Pedidos();
    	$existencia_pedido = $pedido::validarExistenciaPedido($info_pedido['norden']);

    	if (count($existencia_pedido === 0)) {
    		if (!is_null($info_pedido) && is_array($info_pedido)) {
	    		
	    		$pedido->norden = $info_pedido['norden'];
	    		$pedido->idcli = $info_pedido['idcli'];
	    		$pedido->fecha = $info_pedido['fecha'];
	    		$pedido->importe = $info_pedido['importe'];
	    		$pedido->medio_pago = $info_pedido['medio_pago'];
	    		$pedido->cuotas = $info_pedido['cuotas'];
	    		$pedido->valor_desc = $info_pedido['valor_desc'];
	    		$pedido->valor_envio = $info_pedido['valor_envio'];
	    		$pedido->tipo_envio = $info_pedido['tipo_envio'];
	    		$pedido->estado = $info_pedido['estado'];

                /*valido si existe el indice "tracking_number*/
                if (array_key_exists('tracking_number', $info_pedido)) {
                    $pedido->tracking_number = $info_pedido['tracking_number'];

                }

                /*Valido si se paga con Mercado Pago y se guarda el nro de identificacion*/
                if (array_key_exists('nro_mp', $info_pedido)) {
                
                   $pedido->nro_mp = $info_pedido['nro_mp'];
                   $pedido->verificacion_transaccion = $info_pedido['verificacion_transaccion'];

                }

                if (array_key_exists('nro_decidir', $info_pedido)) {
                   $pedido->nro_decidir = $info_pedido['nro_decidir'];
                   $pedido->verificacion_transaccion = $info_pedido['verificacion_transaccion'];

                }

                //var_dump($pedido);die;

	    		$result = $pedido->save();
                
    		} 

    	}

    }

    public static function cancelarPedido($orderId, $motivo) {

        /*Busco en que estado esta el pedido a cancelar, si facturado o en proceso*/        
        $estado_actual_pedido = DB::table('e_pedidos')->Select('estado')->where('norden', $orderId)->get();
        
        if ($estado_actual_pedido[0]->estado == 'ready-for-handling') {
            actualizarEstadoPedido($orderId, 'cancel');
        }
       
        $insert = array(
                    'pedido_cancelacion' => 1,
                    'motivo_cancelacion' => $motivo,
                    'estado' => 'canceled' 
                );
            
                $result = DB::table('e_pedidos')
                        ->where('norden', $orderId)
                        ->update($insert);

    }

    public static function buscarFacturaPedido($pedido = null) {

        if (!is_null($pedido)) {
            $info_pedido = DB::table('e_pedidos')
            ->select('e_pedidos.nro_factura', 'e_pedidos.tracking_number')
            ->Where('e_pedidos.norden', $pedido)
            ->where('e_pedidos.notificado_ecommerce', 1)
            ->get();

            return $info_pedido;
            
        }
    }


    public static function buscarPedidosCanceladosFacturados() {

            $pedidos_cancelados_facturados = DB::table('e_pedidos')
            ->join('e_pedido_item', 'e_pedidos.norden', '=', 'e_pedido_item.orden_id')
            ->select('e_pedidos.*')
            ->where('e_pedidos.notificado_ecommerce', 0)
            ->where(function($query) {
                $query->where('e_pedidos.estado', "canceled")->orWhere('e_pedidos.estado', "invoiced");
            }) 
            ->get();

        return $pedidos_cancelados_facturados;
      
    }

    public static function getInfoPedidoItems($orderId) {

        $info_pedido = DB::table('e_pedido_item')
        ->select('e_pedido_item.sku', 'e_pedido_item.precio', 'e_pedido_item.cantidad')
        ->where('e_pedido_item.orden_id', $orderId)
        ->get();
        return $info_pedido;

    }

    public static function actualizarProcesamientoPedido($orderId = null) {

        $pedido = Pedidos::where('e_pedidos.norden', $orderId)->first();
        
        if (count($pedido) >= 1) {
            // Seteamos un nuevo titulo
            $pedido->notificado_ecommerce = "1";
 
            // Guardamos en base de datos
            $pedido->save();
        }

        
    }

    public static function getInfoPedidoFacturadoSinTrackingNumberIntegral() {

        $info_pedido = DB::select(DB::raw("select e_pedidos_envio.nro_orden, CONCAT(e_pedidos_envio.nombre,' ', e_pedidos_envio.apellido) as nombre_destinatario
            , e_pedidos_envio.domicilio, e_pedidos_envio.ciudad, e_pedidos_envio.c_postal, e_pedidos_envio.telefono from e_pedidos_envio  
            join e_pedidos on e_pedidos.norden = e_pedidos_envio.nro_orden
            where e_pedidos.tipo_envio = 'ENVIO A DOMICILIO INTEGRAL PACK'
            and e_pedidos.tracking_number is NULL
            and e_pedidos.datos_env_integral = 0"));

        /*$info_pedido = DB::table('e_pedidos_envio')
        ->join('e_pedidos', 'e_pedidos.norden', '=', 'e_pedidos_envio.nro_orden')
        ->select('e_pedidos_envio.nro_orden', 'e_pedidos_envio.nombre', 'e_pedidos_envio.apellido','e_pedidos_envio.domicilio', 'e_pedidos_envio.ciudad', 'e_pedidos_envio.c_postal', 'e_pedidos_envio.telefono')
        ->where('e_pedidos.tipo_envio', 'ENVIO A DOMICILIO INTEGRAL PACK')
        ->where('e_pedidos.tracking_number', null)
        ->get();*/

        //$info_pedido = collect($info_pedido);

        return $info_pedido;

    }

    /*Funcion que obtiene los pedidos ya enviados a Integral, 
    para luego obtener los tracking_number de cada uno*/
    public static function obtenerTrackingNumberPedidosIntegral() {

        $info_pedido = DB::select(DB::raw("select e_pedidos.norden 
            from e_pedidos  
            where e_pedidos.tipo_envio = 'ENVIO A DOMICILIO INTEGRAL PACK'
            and (e_pedidos.tracking_number is NULL or e_pedidos.tracking_number = '')
            and e_pedidos.datos_env_integral = 1"));

        return $info_pedido;

    }

    /*Funcion que obtiene los pedidos ya enviados a Urbano, 
    para luego obtener los tracking_number de cada uno*/
    public static function obtenerTrackingNumberPedidosUrbano() {
        $info_pedido = DB::select(DB::raw("select e_pedidos.norden 
            from e_pedidos  
            where (e_pedidos.tipo_envio = 'ENVIO A DOMICILIO URBANO'
            or e_pedidos.tipo_envio = 'ENVIO_DOMICILIO_URBANO')
            and (e_pedidos.tracking_number is not NULL or e_pedidos.tracking_number != '')"));

        return $info_pedido;

    }   

    public static function actualizarEstadoPedidosEnviadosIntegral($pedidos) {
        if (count($pedidos) > 0) {
            
            foreach ($pedidos as $key => $pedido) {
                
                $insert = array(
                    'datos_env_integral' => 1,
                );

                $result = DB::table('e_pedidos')
                        ->where('norden', $pedido->nro_orden)
                        ->update($insert);


            }

        }

    }

    /*Guarda el tracking number traido desde la API de Integral Express*/
    public static function guardarTrackingNumberIntegral($id_pedido, $tracking_number) {

        if (!is_null($id_pedido) && !is_null($tracking_number)) {

            $insert = array(
                    'tracking_number' => $tracking_number,
                );

                $result = DB::table('e_pedidos')
                        ->where('norden', $id_pedido)
                        ->update($insert);

            
        }

    }

    public static function obtenerInfoPedidoAvisoRetiro() {

        $pedidos = DB::table('e_pedidos')
        ->select('e_pedidos.norden', 'e_pedidos.importe', 'e_clientes.nombre', 'e_clientes.apellido', 'e_clientes.e_mail', 'e_pedidos.tipo_envio', 'movi_adi0.empresa')
        ->join('e_clientes', 'e_clientes.dni', '=', 'e_pedidos.idcli')
        ->join('movi_adi0', 'movi_adi0.nnumero', '=', 'e_pedidos.nro_factura')
        ->where('e_pedidos.estado', 'invoiced')
        ->where('e_pedidos.tipo_envio', 'like', '%SUC%')
        ->where('e_pedidos.mail_aviso', 0)
        ->get();

        return $pedidos;

    }

    public static function actualizar_envio_mail_retiro_sucursal($id_pedido) {

        if (!is_null($id_pedido)) {

            $insert = array(
                    'mail_aviso' => 1,
                );

                $result = DB::table('e_pedidos')
                        ->where('norden', $id_pedido)
                        ->update($insert);

            
        }

    }


}





    

    
