<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\EUsers;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Redirect;
use Input;
use Auth;
use View;
use Session;

class AuthController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */
    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'getLogout']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    /*protected function create(array $data)
    {
        return EUsers::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 2,
        ]);

    }*/

    /**
     * Muestra el formulario para login.
    */
    public function getLogin()
    {
 
        // Verificamos que el usuario no esté autenticado
        if (Auth::check())
        {   
            // Si está autenticado lo mandamos a la raíz donde estara el mensaje de bienvenida.
            return Redirect::to('/');
        }
        // Mostramos la vista login.blade.php (Recordemos que .blade.php se omite.)
        return View::make('pages.auth.login');
    }


    public function postLogin() {
        
        /*Obtenemos el role del usuario*/
    
        // Guardamos en un arreglo los datos del usuario.
        $userdata = array(
            'email' => Input::get('email'),
            'password'=> Input::get('password'),
        );
        

        // Validamos los datos y además mandamos como un segundo parámetro la opción de recordar el usuario.
        if (Auth::attempt($userdata, Input::get('remember', 0)) )
        {   
            
            /*Almacenamos en la Session la info principal del usuario logueado*/
            $infoUser = Auth::user();
            Session::push('user.first_name', $infoUser['first_name']);
            Session::push('user.last_name', $infoUser['first_name']);
            Session::push('user.email', $infoUser['email']);
            Session::push('user.role', $infoUser['role']);

            // De ser datos válidos nos mandara a la bienvenida
            if (Session::get('user.role')[0] == "1" ) {
               return Redirect::to('/');
                
            } elseif (Session::get('user.role')[0] == "2" ) {
                return Redirect::to('/catalogo');
                
            } elseif (Session::get('user.role')[0] == "5" ) {
                return Redirect::to('/informes');

            } elseif (Session::get('user.role')[0] == "6" ) {
                return Redirect::to('/pedidos');

            } else {
                return Redirect::to('/catalogoVentas');
                
            }
            
        }

        // En caso de que la autenticación haya fallado manda un mensaje al formulario de login y también regresamos los valores enviados con withInput().
        return Redirect::to('login')
                    ->with('mensaje_error', 'Tus datos son incorrectos')
                    ->withInput();

    }

    public function getLogout() {
 
    	Auth::logout();

        /*Se limpia la variable de Session*/
    	Session::flush();

        return redirect::to('login');

    }

    public function error404() {
        return View::make('pages.errors.404');
    }


}
