<?php

namespace App\Http\Controllers;

use App\Services\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    // favorite list for user
    public function index()
    {
        $nobodyId = NobodyController::HashVerify();
        $favorite = $this->favoriteService->getFavoriteList($nobodyId);
        return response()->json($favorite);
    }

    // add or remove domain
    public function store(Request $request)
    {
        $nobodyId = NobodyController::HashVerify();
        $domain = $request->domain;
        $area = $request->area;

        $result = $this->favoriteService->addOrRemoveFavorite($nobodyId, $domain, $area);
        return response()->json($result);
    }

    // get favorite count
    public function update()
    {
        $nobodyId = NobodyController::HashVerify();
        $counts = $this->favoriteService->getFavoriteCount($nobodyId);
        return response()->json($counts);
    }
}
