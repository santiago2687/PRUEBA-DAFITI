<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use View;
use Session;
use App\models\Tp_adic;
use App\Http\Controllers\Input;
use Yajra\Datatables\Datatables;

class CatalogoVentasController extends Controller
{
    public function _constructor() {
		$this->middleware('auth');

	}

	public function getIndex() {
		$cant_productos = Tp_adic::getCantProductos();
		return View::make('pages.catalogoVentas.catalogoVentas_index')->with('cant_productos', $cant_productos);

	}

	public function showCatalogoVentasDataTable() {
		return Datatables::of(Tp_adic::get_all_product_catalogo_ventas())->make(true);

	}

	public function productoDetalle($sku = null) {
		if (!is_null($sku)) {
			$info_producto = Tp_adic::getDetalleProductoVentas($sku);
			return View::make('pages.catalogoVentas.catalogoVentas_detalle')->with('info_producto', $info_producto);
		}

	}
}
