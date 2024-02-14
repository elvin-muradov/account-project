<?php

namespace App\Traits;

use Exception;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isNull;

trait Searchable
{
    public function scopeSearch(Builder $builder, $filterBy, $logicOperator): Builder
    {
        if (is_array($filterBy)) {
            foreach ($filterBy as $filter) {
                if ($logicOperator === 'and') {
                    switch ($filter['operator']) {
                        case ">":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '>', doubleval($filter['value']));
                            }
                            break;
                        case "<":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '<', doubleval($filter['value']));
                            }
                            break;
                        case ">=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '>=', doubleval($filter['value']));
                            }
                            break;
                        case "<=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '<=', doubleval($filter['value']));
                            }
                            break;
                        case "=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '=', doubleval($filter['value']));
                            }
                            break;
                        case "!=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '<>', doubleval($filter['value']));
                            }
                            break;
                        case "is":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '=', $filter['value']);
                            } elseif ($filter['value']) {
                                $builder->orWhereDate('created_at', '=', $filter['value']);
                            }
                            break;
                        case "isNot":
                            if ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '<>', $filter['value']);
                            } elseif ($filter['value']) {
                                $builder->whereDate('created_at', '<>', $filter['value']);
                            }
                            break;
                        case "not":
                            if ($filter['field'] && $filter['value']) {
                                $builder->whereDate('created_at', '<>', $filter['value']);
                            }
                            break;

                        case "after":
                            if ($filter['field'] && $filter['value']) {
                                $builder->whereDate('created_at', '>', $filter['value']);
                            }
                            break;

                        case "onOrAfter":
                            if ($filter['field'] && $filter['value']) {
                                $builder->whereDate('created_at', '>=', $filter['value']);
                            }
                            break;

                        case "before":
                            if ($filter['field'] && $filter['value']) {
                                $builder->whereDate('created_at', '<', $filter['value']);
                            }
                            break;

                        case "onOrBefore":
                            if ($filter['field'] && $filter['value']) {
                                $builder->whereDate('created_at', '<=', $filter['value']);
                            }
                            break;

                        case "contains":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->whereRelation(
                                    $relation, $relationColumn, 'ilike', '%' . $filter['value'] . '%'
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], 'ilike', '%' . $filter['value'] . '%');
                            }
                            break;

                        case
                        "equals":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->whereRelation(
                                    $relation, $relationColumn, '=', $filter['value']
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '=', $filter['value']);
                            }
                            break;

                        case "startsWith":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->whereRelation(
                                    $relation, $relationColumn, 'ilike', $filter['value'] . '%'
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], 'ilike', $filter['value'] . '%');
                            }
                            break;

                        case "endsWith":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->whereRelation(
                                    $relation, $relationColumn, 'ilike', '%' . $filter['value']
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], 'ilike', '%' . $filter['value']);
                            }
                            break;

                        case "isEmpty":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->whereRelation(
                                    $relation, $relationColumn, '=', null
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '=', null);
                            }
                            break;

                        case "isNotEmpty":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->whereRelation(
                                    $relation, $relationColumn, '<>', null
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->where($filter['field'], '<>', null);
                            }
                            break;

                        case "isAnyOf":
                            $values = collect($filter['value'])->map(function ($value) {
                                return trim($value);
                            })->toArray();

                            if ($filter['field'] && $filter['value']) {
                                $builder->whereIn($filter['field'], $values);
                            }
                            break;

                        case "notIsAnyOf":
                            $values = collect($filter['value'])->map(function ($value) {
                                return trim($value);
                            })->toArray();

                            if ($filter['field'] && $filter['value']) {
                                $builder->whereNotIn($filter['field'], $values);
                            }
                            break;
                        default:
                            $builder;
                            break;
                    }
                    continue;
                } else {
                    switch ($filter['operator']) {
                        case ">":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '>', doubleval($filter['value']));
                            }
                            break;
                        case "<":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '<', doubleval($filter['value']));
                            }
                            break;
                        case ">=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '>=', doubleval($filter['value']));
                            }
                            break;
                        case "<=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '<=', doubleval($filter['value']));
                            }
                            break;
                        case "=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '=', doubleval($filter['value']));
                            }
                            break;
                        case "!=":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '<>', doubleval($filter['value']));
                            }
                            break;
                        case "is":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '=', $filter['value']);
                            } elseif ($filter['value']) {
                                $builder->orWhereDate('created_at', '=', $filter['value']);
                            }
                            break;
                        case "isNot":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '<>', $filter['value']);
                            } elseif ($filter['value']) {
                                $builder->orWhereDate('created_at', '<>', $filter['value']);
                            }
                            break;
                        case "not":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereDate('created_at', '<>', $filter['value']);
                            }
                            break;
                        case "after":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereDate('created_at', '>', $filter['value']);
                            }
                            break;

                        case "onOrAfter":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereDate('created_at', '>=', $filter['value']);
                            }
                            break;

                        case "before":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereDate('created_at', '<', $filter['value']);
                            }
                            break;

                        case "onOrBefore":
                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereDate('created_at', '<=', $filter['value']);
                            }
                            break;

                        case "contains":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->orWhereRelation(
                                    $relation, $relationColumn, 'ilike', '%' . $filter['value'] . '%'
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], 'ilike', '%' . $filter['value'] . '%');
                            }
                            break;

                        case "equals":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->orWhereRelation(
                                    $relation, $relationColumn, '=', $filter['value']
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '=', $filter['value']);
                            }
                            break;

                        case "startsWith":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->orWhereRelation(
                                    $relation, $relationColumn, 'ilike', $filter['value'] . '%'
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], 'ilike', $filter['value'] . '%');
                            }
                            break;

                        case "endsWith":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->orWhereRelation(
                                    $relation, $relationColumn, 'ilike', '%' . $filter['value']
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], 'ilike', '%' . $filter['value']);
                            }
                            break;

                        case "isEmpty":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->orWhereRelation(
                                    $relation, $relationColumn, '=', null
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '=', null);
                            }
                            break;

                        case "isNotEmpty":
                            if (isset($filter['field']) && Str::contains($filter['field'], '.')) {
                                $relation = explode('.', $filter['field'])[0];
                                $relationColumn = explode('.', $filter['field'])[1];
                                $builder->orWhereRelation(
                                    $relation, $relationColumn, '<>', null
                                );
                            } elseif ($filter['field'] && $filter['value']) {
                                $builder->orWhere($filter['field'], '<>', null);
                            }
                            break;

                        case "isAnyOf":
                            $values = collect($filter['value'])->map(function ($value) {
                                return trim($value);
                            })->toArray();

                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereIn($filter['field'], $values);
                            }
                            break;
                        case "notIsAnyOf":
                            $values = collect($filter['value'])->map(function ($value) {
                                return trim($value);
                            })->toArray();

                            if ($filter['field'] && $filter['value']) {
                                $builder->orWhereNotIn($filter['field'], $values);
                            }
                            break;
                        default:
                            $builder;
                            break;
                    }
                }
            }
        }

        return $builder;
    }
}
