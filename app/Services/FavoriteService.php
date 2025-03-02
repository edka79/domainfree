<?php

namespace App\Services;

use App\Models\Agregation;
use App\Models\DomainFree;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class FavoriteService
{
    public function getFavoriteList($nobodyId)
    {
        return Favorite::where('nobody_id', $nobodyId)
            ->leftJoin('agregations as ag', 'ag.domain', '=', 'favorites.domain')
            ->select('datecreate', 'area', 'favorites.domain', DB::raw('DATEDIFF(ag.date_free, CURDATE()) as date_free'))
            ->get();
    }

    public function addOrRemoveFavorite($nobodyId, $domain, $area)
    {
        $favorite = Favorite::where('nobody_id', $nobodyId)->where('domain', $domain);

        if ($favorite->exists()) {
            $favorite->delete();
            return ['action' => 'delete'];
        } else {
            $data = $area == 'search' ? Agregation::where('domain', $domain)->first() : DomainFree::where('domain', $domain)->first();
            Favorite::create([
                'area' => $area,
                'domain' => $domain,
                'nobody_id' => $nobodyId,
                'data' => $data->toJson()
            ]);
            return ['action' => 'add'];
        }
    }

    public function getFavoriteCount($nobodyId)
    {
        return [
            'all' => Favorite::where('nobody_id', $nobodyId)->count(),
            'search' => Favorite::where('nobody_id', $nobodyId)
                ->leftJoin('agregations', 'agregations.domain', '=', 'favorites.domain')
                ->where('favorites.area', 'search')
                ->whereNotNull('agregations.id')
                ->count(),
            'free' => Favorite::where('nobody_id', $nobodyId)
                ->where('favorites.area', 'free')
                ->count(),
        ];
    }
}
