<?php

namespace App\Http\Controllers;

use App\Models\Agregation;
use App\Models\DomainFree;
use App\Models\DomainImportExpired;
use App\Models\Favorite;
use App\Models\User;
use App\Models\UsersNobody;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FavoriteController extends Controller
{

    // favorite list for user
    public function index()
    {
        $favorite = Favorite::where('nobody_id', NobodyController::HashVerify())
        ->leftJoin('agregations as ag', 'ag.domain', '=', 'favorites.domain')
        ->select('datecreate', 'area', 'favorites.domain', DB::raw('DATEDIFF(ag.date_free, CURDATE()) as date_free'))
        ->get();
        return response()->json($favorite);
    }

    // add or remove domain
    public function store()
    {
        $nobodyId = NobodyController::HashVerify();
        $favorite = Favorite::where('nobody_id', $nobodyId)->where('domain', \Request()->domain);
        if($favorite->count() > 0){
            $favorite->delete();
            return response()->json(['action' => 'delete']);
        }
            else {
                $area = \Request()->area;
                $domain = \Request()->domain;
                $data = $area == 'search' ? Agregation::where('domain', $domain)->first() : DomainFree::where('domain', $domain)->first();
                Favorite::insert([
                    'area' => $area,
                    'domain' => $domain,
                    'nobody_id' => $nobodyId,
                    'data' => $data->toJson()
                ]);
                return response()->json(['action' => 'add']);
            }

    }

    // get favorite count
    public function update()
    {
        $nobody = NobodyController::HashVerify();
        return [
            'all' => Favorite::where('nobody_id', '=', $nobody)->count(),
            'search' => Favorite::where('nobody_id', '=', $nobody)
                ->leftJoin('agregations', 'agregations.domain', '=', 'favorites.domain')
                ->where('favorites.area', 'search')
                ->whereNotNull('agregations.id')
                ->count(),
            'free' => Favorite::where('nobody_id', '=', $nobody)
                ->where('favorites.area', 'free')
                ->count(),
        ];
    }



}
