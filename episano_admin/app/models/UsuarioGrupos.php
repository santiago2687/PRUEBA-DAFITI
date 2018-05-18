<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use DB;

class UsuarioGrupos extends Model
{
    protected $table = 'e_users_groups';

    public static function listUserGroup() {
    	$user_groups = DB::table('e_users_groups')->select('id', 'group_name')->get();
    	return $user_groups;

    }
}
