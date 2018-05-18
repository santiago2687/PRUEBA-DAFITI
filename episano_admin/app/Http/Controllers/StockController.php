<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use View;
use App\models\Stock;
use Yajra\Datatables\Datatables;

class StockController extends Controller
{	
	protected $stock;

    public function _constructor() {
		$this->middleware('auth');

	}

	public function getIndex() {
		//$stock = Stock::getStock();
		//return View::make('pages.stock.stock_index')->with('stock', $stock); 
		return View::make('pages.stock.stock_index');
	
	}

	public function showStockDataTable() {	
    	return Datatables::of(Stock::getStock())->make(true);

	}

	public function modificarStock() {
		return View::make('pages.stock.stock_index'); 

	}
}
