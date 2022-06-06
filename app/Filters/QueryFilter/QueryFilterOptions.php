<?php

namespace App\Filters\QueryFilter;

use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;

class QueryFilterOptions
{
    public $options = [
        'allowedFilters' => [],
        'exactFilters' => [],
        'scopeFilters' => [],
        'callbackFilters' => [],
        'customFilters' => []
    ];

    public $trashed = false;

    public static function make()
    {
        return new static;
    }

    public function allowedFilters(array $filters)
    {
        $this->options['allowedFilters'] = $filters;

        return $this;
    }

    public function exactFilters(array $filters)
    {
        $this->options['exactFilters'] = $filters;

        return $this;
    }

    public function scopeFilters(array $filters)
    {
        $this->options['scopeFilters'] = $filters;

        return $this;
    }

    public function callbackFilters(array $filters)
    {
        $this->options['callbackFilters'] = $filters;

        return $this;
    }

    public function customFilters(array $filters)
    {
        $this->options['customFilters'] = $filters;

        return $this;
    }

    public function trashed()
    {
        $this->trashed = true;

        return $this;
    }

    public function filters(): array
    {
        if ($this->trashed) {
            $this->options[] = AllowedFilter::trashed();
        }

        return Arr::flatten($this->options);
    }
}
