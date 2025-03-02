<?php

namespace App\Http\Controllers;

use App\Models\Agregation;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{

    public function index()
    {
        // разбор фильтра
        $filters = [ 'and' => [], 'or' => [] ];
        if (\Request('filter')){
            //foreach(explode(',"and",', str_replace(['[', ']', ' '], '', \Request('filter'))) as $and){ // todo возможно будет баг
            foreach(explode(',"and",', str_replace(['[', ']'], '', \Request('filter'))) as $and){
                $rows = [];
                // если это OR
                if (stripos($and, ',"or",')){
                    foreach(explode(',"or",', str_replace(['[', ']', ' '], '', $and)) as $or){
                        $t = explode(',', str_replace('"', '', $or));
                        $rows[] = count($t) == 3 ? $t : array_merge($t, ['']);
                    }
                    $filters['or'][] = $rows;
                }
                else {  // или AND
                    $t = explode(',', str_replace('"', '', $and));
                    // обработка исключительной ситуации с !=
                    if(count($t) > 3) $filters['and'][] = [ 0 => $t[1], 1 => '<>', 2 => $t[3] ];
                        else $filters['and'][] = count($t) == 3 ? $t : array_merge($t, ['']);
                }
            }
        }

        //dd($filters);

        // Группировка
        $group = false;
        if (\Request('group')){
            $group = json_decode(\Request('group'), true)[0]['selector'];
        }

        // по умолчанию всегда сортируем по: id, asc
        $fieldId = 'agr.id';
        if (\Request('area') == 'free'){
            $fieldId = 'free.id';
        }
        $orderByField = $group ? $group : $fieldId;
        $orderByWay = 'asc';

        // или если сортировка была задана явно
        if (\Request('sort')){
            $sort = json_decode(\Request('sort'), true);
            $orderByField = $sort[0]['selector'];
            $orderByWay = $sort[0]['desc'] ? 'desc': 'asc';
        }

        $area = \Request('area');

        // Поиск освобождающихся доменов
        if ($area == 'search') {
            $query = DB::table('agregations as agr')
                ->leftJoin('domain_dir_zones as zones', 'zones.id', '=', 'agr.zone_id')
                ->leftJoin('favorites', function ($join) {
                    $join->on('favorites.domain', '=', 'agr.domain');
                    $join->where('nobody_id', '=', NobodyController::HashVerify());
                })
                ->select(
                    'agr.id as agr__id',
                    'agr.domain as agr__domain',
                    DB::raw("(CASE WHEN favorites.id is not null THEN 'Да' ELSE null END) as favorite"),
                    'zones.zone_alias as zones__zone_alias',
                    'agr.age as agr__age',
                    'agr.litera_count as agr__litera_count',
                    'agr.litera_attr as agr__litera_attr',
                    'agr.is_keyword as agr__is_keyword',
                    'agr.keyword_word as agr__keyword_word',
                    'agr.date_free as agr__date_free',
                    'agr.expired_iks as agr__expired_iks',
                    'agr.expired_links as agr__expired_links',
                    'agr.days_for_free as agr__days_for_free'
                    //DB::raw('DATEDIFF(agr.date_free, CURDATE()) as days_for_free')
                )
                ->where(function ($where) use ($filters) {
                    // Все AND условия
                    foreach ($filters['and'] as $filter) {
                        $rule = self::checkRules($filter);
                        $where->where($rule[0], $rule[1], $rule[2]);
                    }
                    // Все OR условия
                    foreach ($filters['or'] as $filter) {
                        $where->where(function ($where) use ($filter) {
                            foreach ($filter as $fil) {
                                $rule = self::checkRules($fil);
                                $where->orWhere($rule[0], $rule[1], $rule[2]);
                            }
                        });
                    }
                })
                ->orderBy($orderByField, $orderByWay);
        }



        // Поиск свободных доменов
        if ($area == 'free') {
            $selectGroup = $group ? str_replace('__', '.', $group).' as '.$group : [];
            $select = $group ? $selectGroup : ['free.id as free__id', 'free.domain as free__domain', 'free.word as free__word', 'free.translate as free__translate', 'free.zone as free__zone',
                        'free.word_type as free__word_type', 'free.word_type as free__word_type',
                        'free.litera_count as free__litera_count', 'free.litera_attr as free__litera_attr'];
            if(!$group) {
                $select[] = DB::raw("(CASE WHEN favorites.id is not null THEN 'Да' ELSE null END) as favorite");
            }
            $query = DB::table('domain_free as free')
                ->select($select)
                ->where(function ($where) use ($filters) {
                    // Все AND условия
                    foreach ($filters['and'] as $filter) {
                        $rule = self::checkRules($filter);
                        $where->where($rule[0], $rule[1], $rule[2]);
                    }
                    // Все OR условия
                    foreach ($filters['or'] as $filter) {
                        $where->where(function ($where) use ($filter) {
                            foreach ($filter as $fil) {
                                $rule = self::checkRules($fil);
                                $where->orWhere($rule[0], $rule[1], $rule[2]);
                            }
                        });
                    }
                })
                ->orderBy($orderByField, $orderByWay);  // сортировку нужно делать именно тут, чтобы ее не обрезал лимит и так быстрее
                if(!$group){
                    $query->leftJoin('favorites', function ($join) {
                        $join->on('favorites.domain', '=', 'free.domain');
                        $join->where('favorites.nobody_id', NobodyController::HashVerify());
                        $join->where('favorites.area', 'free');
                    });
                }
        }


            // лимит и смещение делаем тут, через переменные (и по умолчанию)
            $limit = (int)\Request('take');
            $offset = (int)\Request('skip');

            // https://js.devexpress.com/Demos/WidgetsGallery/Demo/DataGrid/WebAPIService/Vue/Light/ - дока по группировке данных

            if ($area == 'search'){
                $data = $group ? $query->groupBy($group)->offset($offset)->limit($limit)->get() : $query->offset($offset)->limit($limit)->get();
                $count = $group ? count($data) : $query->count(); // нужно так
            }
            if ($area == 'free'){
                $data = $group ? $query->groupBy($group)->offset($offset)->limit($limit)->get() : $query->offset($offset)->limit($limit)->get();
                $count = $group ? count($data) : 10000;
            }


            if ($group){
                $data = $data->map(function($item) use ($orderByField){
                    return ['key' => $item->$orderByField];
                });
            }

            return response([
                'data' => $data,
                'totalCount' => $count
            ]);
    }



    // проверка и/или преобразование условий
    public static function checkRules($filter){
        $states = [
            'contains' => 'LIKE',
            'notcontains' => 'NOT LIKE',
            'startswith' => 'LIKE',
            'endswith' => 'LIKE',
            '=' => '=',
            '<>' => '!=',
            '>=' => '>=',
            '<=' => '<=',
        ];
        if ($filter[1] == 'contains' or $filter[1] == 'notcontains') $filter[2] = '%' . $filter[2] . '%';
        if ($filter[1] == 'startswith') $filter[2] = $filter[2] . '%';
        if ($filter[1] == 'endswith') $filter[2] = '%' . $filter[2];
        $filter[1] = isset($states[$filter[1]]) ? $states[$filter[1]] : '=';

        // обработка алиасов для всех запросов в значение: таблица.поле
        $filter[0] = str_replace('__', '.', $filter[0]);

        // специальная логика для фильтрации отдельных полей
            // favorite
            if($filter[0] == 'favorite'){
                $filter = [ 'favorites.id', '!=', '0' ];
            }
            // domain keyword
            if($filter[0] == 'agr.is_keyword'  and $filter[2] != 'Да'){
                $filter[0] = 'agr.keyword_word';
            }

        return $filter;
    }


}
