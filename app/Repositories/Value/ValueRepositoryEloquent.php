<?php

namespace App\Repositories\Value;

use App\Enums\ValueType;
use App\Models\Column;
use App\Models\Value;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ValueRepositoryEloquent extends BaseRepository implements ValueRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        // $value = Value::all()->each(function ($value) {
        //     $value->value = $this->parseType($value->value, $value->column->type);
        // })->toQuery();
        // dd($value->get());
        // return $value;
        return Value::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

    public function getParsed($id = null)
    {
        if (!$id) {
            return Value::all()->each(function ($value) {
                $value->value = $this->parseType($value->value, $value->column->type);
            });
        }

        $value = Value::findOrFail($id);
        $value->value = $this->parseType($value->value, $value->column->type);
        return $value;
    }

    public function parseType($value, $type)
    {
        switch ($type) {
            case ValueType::VARCHAR()->value:
                parse_str($value, $value);
                break;
            case ValueType::INTEGER()->value:
                $value = intval($value);
                break;
            case ValueType::DECIMAL()->value:
                $value = floatval($value);
                break;
            case ValueType::BOOLEAN()->value:
                $value = boolval($value);
                break;
            default:
                $value = $value;
                break;
        }

        return $value;
    }

    public function customPaginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
