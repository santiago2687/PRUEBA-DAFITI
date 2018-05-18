<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use View;
use Session;
use App\EUsers;
use App\models\UsuarioGrupos;
use DB;

class UserController extends Controller
{

    public function _constructor() {
		$this->middleware('auth');

	}

    public function register() {

        if (Session::get('user.role')[0] == 1) {

        	$user_groups = UsuarioGrupos::listUserGroup();

        	$data['user_groups'] = $user_groups;

            return View::make('pages.auth.register')->with('data', $data);

        } else {
            echo "Debe ser administrador para poder acceder a esta sección";

        }

    }


    public function createUser(Request $request) {

    	$exist_user_email = EUsers::validateUserEmail($request->input("email"));

    	$user_groups = UsuarioGrupos::listUserGroup();
    	
    	if ($exist_user_email == "true") {
    		$new_user = EUsers::create([
		        'first_name' => $request->input("first_name"),
		        'last_name' => $request->input("last_name"),
		        'email' => $request->input("email"),
		        'password' => bcrypt($request->input("password")),
		        'role' => (int)$request->input("role"),
    		]);

    		$msg = "Se ha creado el usuario correctamente!";

    		$data = array('user_groups' => $user_groups, 'msg' => $msg, 'result' => "true");
    		
    		return View::make('pages.auth.register')->with('data', $data);

    	} else {

    		$msg = "Ya existe el mail seleccionado";

    		$data = array('user_groups' => $user_groups, 'msg' => $msg, 'result' => "false");

    		return View::make('pages.auth.register')->with('data', $data);

    	}

    	



    }

        
}
