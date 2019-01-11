<?php

namespace App\Models\System;

trait DataViewer
{
    public function scopeExport($query)
    {
        return $this->filter($query)->get();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeAdvancedFilter($query)
    {
        return $this->filter($query)
            ->paginate(request('limit', 10));
    }

    /**
     * @param $query
     * @return mixed
     */
    private function filter($query)
    {
        $direction = request('order_direction', 'descending');
        $direction = ($direction === 'descending')?'desc':'asc';
        return $this->process($query)
            ->orderBy(request('order_column', 'created_at'), $direction);
    }

    private function process($query)
    {
//        $filter_value = array_key_exists('filter_value', $data)?$data['filter_value']:'';
//        return $query->where($data['filter_column'], 'like', '%'.$filter_value.'%');

        return (new QueryBuilder)->apply($query, request()->all());
    }
}