<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use DB;

class EUsers extends Model implements AuthenticatableContract
{	

	use Authenticatable;
    protected $table = 'e_users';

    protected $fillable = ['first_name', 'last_name', 'email', 'password', 'role'];

    public static function getRoleUser($email = null) {
    	if (!is_null($email)) {
    		$role = DB::table('e_users')->select('role')->Where('email', $email)->get();
	    	return $role;

    	}

    } 

    public static function validateUserEmail($email = null) {
    	if (!is_null($email)) {
    		$email = DB::table('e_users')->select('email')->Where('email', $email)->get();

    		if (count($email) > 0) {
    			return "false";

    		} else {
    			return "true";

    		}
	    	

    	}
    }
}
