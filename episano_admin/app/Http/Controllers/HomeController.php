<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use View;
use Session;

class HomeController extends Controller
{
	public function _constructor() {
		$this->middleware('auth');

	}

	public function getIndex() {
                                     		
		/*Se le pasa la info del usuario logueado a la vista*/	
		return View::make('pages.home'); 

	}
}
