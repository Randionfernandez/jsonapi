<?php

namespace App\JsonApi;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JsonApiQueryBuilder
{
    public function allowedSorts(): Closure
    {
        return function ($allowedSorts) {
            /** @var Builder $this */
            if (request()->filled('sort')) {
                $sortFields = explode(',', request()->input('sort'));

                foreach ($sortFields as $sortField) {
                    $sortDirection = Str::of($sortField)->startsWith('-') ? 'desc' : 'asc';
                    $sortField = ltrim($sortField, '-');
// si el primer parámetro retorna true, devuelve un estado 400
                    abort_unless(in_array($sortField, $allowedSorts), 400);
                    $this->orderBy($sortField, $sortDirection);
                }
            }
            return $this;
        };
    }

    public function allowedFilters(): Closure
    {
        return function ($allowedFilters) {
            /** @var Builder $this */
            foreach (request('filter', []) as $filter => $value) {
                abort_unless(in_array($filter, $allowedFilters), 400);
                $this->hasNamedScope($filter) ?
                    $this->{$filter}($value) :
                    $this->where($filter, 'LIKE', '%' . $value . '%');
            };
            return $this;
        };
    }
/**
 *@todo manejar más de un query parameter de tipo fields[] en el mismo query string
 */
    public function sparseFieldset()
    {
        return function () {
            /** @var Builder $this */
            if (request()->isNotFilled('fields')) {
                return $this;
            }
//            dd(request('fields.articles'));
            $resourceType = $this->model->getTable();
            if (property_exists($this->model, 'resourceType')) {
                $resourceType = $this->model->resourceType;
            }
            $fields = explode(',', request('fields.' . $resourceType));

            $routeKeyName = $this->model->getRouteKeyName();
            if (!in_array($routeKeyName, $fields))
                $fields[] = $routeKeyName;
            return $this->addSelect($fields);
        };
    }

    public function jsonPaginate(): Closure
    {
        return function () {
            /** @var Builder $this */
            return $this->paginate(  // en este contexto $this es la clase que estamos extendiendo y debemos retornarlo
                $perPage = request('page.size', 15),
                $columns = ['*'],
                $pageName = 'page[number]',
                $page = request('page.number', 1),
            )->appends(request()->only('sort', 'filter', 'field', 'page'));
        };
    }
}
