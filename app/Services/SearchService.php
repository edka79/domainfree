<?php

namespace App\Services;

use App\Http\Controllers\NobodyController;
use App\Models\Agregation;
use App\Models\Favorite;
use Illuminate\Support\Facades\DB;

class SearchService
{
    // Разбор фильтров
    public function parseFilters($filter)
    {
        $filters = ['and' => [], 'or' => []];
        if ($filter) {
            foreach (explode(',"and",', str_replace(['[', ']'], '', $filter)) as $and) {
                $rows = [];
                // Если это OR
                if (stripos($and, ',"or",')) {
                    foreach (explode(',"or",', str_replace(['[', ']', ' '], '', $and)) as $or) {
                        $t = explode(',', str_replace('"', '', $or));
                        $rows[] = count($t) == 3 ? $t : array_merge($t, ['']);
                    }
                    $filters['or'][] = $rows;
                } else {  // Или AND
                    $t = explode(',', str_replace('"', '', $and));
                    // Обработка исключительной ситуации с !=
                    if (count($t) > 3) {
                        $filters['and'][] = [0 => $t[1], 1 => '<>', 2 => $t[3]];
                    } else {
                        $filters['and'][] = count($t) == 3 ? $t : array_merge($t, ['']);
                    }
                }
            }
        }
        return $filters;
    }

    // Проверка и преобразование условий
    public function checkRules($filter)
    {
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
        if ($filter[1] == 'contains' || $filter[1] == 'notcontains') {
            $filter[2] = '%' . $filter[2] . '%';
        }
        if ($filter[1] == 'startswith') {
            $filter[2] = $filter[2] . '%';
        }
        if ($filter[1] == 'endswith') {
            $filter[2] = '%' . $filter[2];
        }
        $filter[1] = $states[$filter[1]] ?? '=';

        // Обработка алиасов для всех запросов в значение: таблица.поле
        $filter[0] = str_replace('__', '.', $filter[0]);

        // Специальная логика для фильтрации отдельных полей
        if ($filter[0] == 'favorite') {
            $filter = ['favorites.id', '!=', '0'];
        }
        if ($filter[0] == 'agr.is_keyword' && $filter[2] != 'Да') {
            $filter[0] = 'agr.keyword_word';
        }

        return $filter;
    }

    // Поиск освобождающихся доменов
    public function searchAgregations($filters, $orderByField, $orderByWay, $group, $offset, $limit)
    {
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
            )
            ->where(function ($where) use ($filters) {
                // Все AND условия
                foreach ($filters['and'] as $filter) {
                    $rule = $this->checkRules($filter);
                    $where->where($rule[0], $rule[1], $rule[2]);
                }
                // Все OR условия
                foreach ($filters['or'] as $filter) {
                    $where->where(function ($where) use ($filter) {
                        foreach ($filter as $fil) {
                            $rule = $this->checkRules($fil);
                            $where->orWhere($rule[0], $rule[1], $rule[2]);
                        }
                    });
                }
            })
            ->orderBy($orderByField, $orderByWay);

        if ($group) {
            $data = $query->groupBy($group)->offset($offset)->limit($limit)->get();
            $count = count($data);
        } else {
            $data = $query->offset($offset)->limit($limit)->get();
            $count = $query->count();
        }

        return [$data, $count];
    }

    // Поиск свободных доменов
    public function searchDomainFree($filters, $orderByField, $orderByWay, $group, $offset, $limit)
    {
        $selectGroup = $group ? str_replace('__', '.', $group) . ' as ' . $group : [];
        $select = $group ? $selectGroup : [
            'free.id as free__id',
            'free.domain as free__domain',
            'free.word as free__word',
            'free.translate as free__translate',
            'free.zone as free__zone',
            'free.word_type as free__word_type',
            'free.litera_count as free__litera_count',
            'free.litera_attr as free__litera_attr'
        ];

        if (!$group) {
            $select[] = DB::raw("(CASE WHEN favorites.id is not null THEN 'Да' ELSE null END) as favorite");
        }

        $query = DB::table('domain_free as free')
            ->select($select)
            ->where(function ($where) use ($filters) {
                // Все AND условия
                foreach ($filters['and'] as $filter) {
                    $rule = $this->checkRules($filter);
                    $where->where($rule[0], $rule[1], $rule[2]);
                }
                // Все OR условия
                foreach ($filters['or'] as $filter) {
                    $where->where(function ($where) use ($filter) {
                        foreach ($filter as $fil) {
                            $rule = $this->checkRules($fil);
                            $where->orWhere($rule[0], $rule[1], $rule[2]);
                        }
                    });
                }
            });

        if (!$group) {
            $query->leftJoin('favorites', function ($join) {
                $join->on('favorites.domain', '=', 'free.domain');
                $join->where('favorites.nobody_id', NobodyController::HashVerify());
                $join->where('favorites.area', 'free');
            });
        }

        $query->orderBy($orderByField, $orderByWay);

        if ($group) {
            $data = $query->groupBy($group)->offset($offset)->limit($limit)->get();
            $count = count($data);
        } else {
            $data = $query->offset($offset)->limit($limit)->get();
            $count = 10000; // По умолчанию для свободных доменов
        }

        return [$data, $count];
    }
}
