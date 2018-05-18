<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use View;
use Session;
use App\Helper\PedidoHelper;
use App\models\Pedidos;
use App\Http\Controllers\Input;
use Yajra\Datatables\Datatables;

class PedidoController extends Controller
{
    public function _constructor() {
		$this->middleware('auth');
		
	}

	public function getIndex() {
		$cant_pedidos = Pedidos::getCantPedidos();
		return View::make('pages.pedido.pedido_index')->with('cant_pedidos', $cant_pedidos); 
		
	}

	public function showPedidosDataTable() {
		return Datatables::of(Pedidos::mostrarPedidos())->make(true);
	}
	
	/*Funcion que muestra el detalle de un pedido en particular segun el id del pedido*/
	public function detallePedido ($orderId = null) {
		if (!is_null($orderId)) {
			$order_info = Pedidos::getInfoPedido($orderId);
			return View::make('pages.pedido.pedido_detalle')->with('order_info', $order_info);

		}
	}

	public function validarPedidosNuevos(Request $request, $ultimo_pedido) {

		if($request->ajax()){
			$resultado = Pedidos::validarCantidadPedidos($ultimo_pedido);
			if ($resultado  === true) {
				return "0";

			} else {
				return "1";

			}

		}

	}

	public function cancelar_pedido(Request $request) {
		$orderId = $request->input("id_pedido_cancelar");
		$motivo = $request->input("motivo_cancelacion");
		
		if (!is_null($orderId) && !is_null($motivo)) {
			Pedidos::cancelarPedido($orderId, $motivo);
			$cant_pedidos = Pedidos::getCantPedidos();
			return redirect('/pedidos');
		}
		
	}

}
