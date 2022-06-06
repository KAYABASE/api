<?php

namespace App\Filters\RelationFilter;

use Illuminate\Support\Arr;

class RelationFilterOptions
{
    public $options = [
        'relationshipFilters' => [],
        'countFilters' => [],
        'customFilters' => [],
    ];

    public static function make()
    {
        return new static;
    }

    public function relationshipFilters(array $filters)
    {
        $this->options['relationshipFilters'] = $filters;

        return $this;
    }

    public function countFilters(array $filters)
    {
        $this->options['countFilters'] = $filters;

        return $this;
    }

    public function customFilters(array $filters)
    {
        $this->options['customFilters'] = $filters;

        return $this;
    }

    public function filters(): array
    {
        return Arr::flatten($this->options);
    }
}
