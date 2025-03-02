<?php

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function index(Request $request)
    {
        // Разбор фильтров
        $filters = $this->searchService->parseFilters($request->filter);

        // Группировка
        $group = false;
        if ($request->group) {
            $group = json_decode($request->group, true)[0]['selector'];
        }

        // Сортировка
        $fieldId = $request->area == 'free' ? 'free.id' : 'agr.id';
        $orderByField = $group ? $group : $fieldId;
        $orderByWay = 'asc';
        if ($request->sort) {
            $sort = json_decode($request->sort, true);
            $orderByField = $sort[0]['selector'];
            $orderByWay = $sort[0]['desc'] ? 'desc' : 'asc';
        }

        // Лимит и смещение
        $limit = (int)$request->take;
        $offset = (int)$request->skip;

        // Поиск данных
        $area = $request->area;
        if ($area == 'search') {
            [$data, $count] = $this->searchService->searchAgregations($filters, $orderByField, $orderByWay, $group, $offset, $limit);
        } elseif ($area == 'free') {
            [$data, $count] = $this->searchService->searchDomainFree($filters, $orderByField, $orderByWay, $group, $offset, $limit);
        }

        // Группировка данных
        if ($group) {
            $data = $data->map(function ($item) use ($orderByField) {
                return ['key' => $item->$orderByField];
            });
        }

        return response([
            'data' => $data,
            'totalCount' => $count
        ]);
    }
}
