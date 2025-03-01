<?php

namespace App\Http\Controllers;

use App\Models\DomainImportExpired;
use App\Models\UsersNobody;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class NobodyController extends Controller
{

    public function __construct()
    {
        //
    }

    public function index()
    {
        $hash = md5(rand(111, 9999999999).time());
        $id = UsersNobody::insertGetId([ 'hash' => $hash ]);
            return $id.':'.$hash;
    }

    // verify hash
    public static function HashVerify($hash = null)
    {
        if (!$hash){
            $hash = \Request()->header('nobody');
        }
        $hash = explode(':', $hash);
        if(count($hash) != 2){
            return 0;
        }
        $res = UsersNobody::where('id', $hash[0] ?? 0)->first();
        if(!empty($res->hash) and $res->hash == $hash[1]){
            return $res->id;
        }
            else return 0;
    }


}
