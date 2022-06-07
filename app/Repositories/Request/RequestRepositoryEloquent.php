<?php

namespace App\Repositories\Request;

use App\Http\Requests\Row\RowStoreRequest;
use App\Models\Request;
use App\Models\Table;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class RequestRepositoryEloquent extends BaseRepository implements RequestRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return Request::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

    const Payload = [
        'values' => [
            [
                'column_id' => 1,
                'value' => 'value1',
            ]
        ]
    ];

    public function makeShowEp(Table $table, $request)
    {
        $requestBody = $request->validated();

        $_request = Request::where('method', 'GET')->where('table_id', $table->id)->first();
        if (isset($requestBody['filter'])) {
            $_request = Request::where('method', 'GET')->where('filter', json_encode($requestBody['filter']['ids']))->where('table_id', $table->id)->first();
        }

        if ($_request) {
            return $_request;
        }

        $query = uniqid();
        $_request = Request::create([
            'method' => 'GET',
            'table_id' => $table->id,
            'query' => $query,
            'filter' => isset($requestBody['filter']) ? json_encode($requestBody['filter']['ids']) : "",
        ]);
        return $_request;
    }

    public function makeStoreEp(Table $table, $request)
    {
        $requestBody = $request->validated();
        $_request = Request::where('method', 'POST')->where('table_id', $table->id)->first();
        if ($_request) {
            return $_request;
        }

        $query = uniqid();
        $_request = Request::create([
            'method' => 'POST',
            'table_id' => $table->id,
            'query' => $query,
            'payload' => '{
                "values": [
                    {
                        "column_id": 1,
                        "value": "ABC"
                    }
                ]
            }',
        ]);
        return $_request;
    }

    public function makeUpdateEp(Table $table, $request)
    {
        $requestBody = $request->validated();
        $_request = Request::where('method', 'PUT')->where('table_id', $table->id)->first();
        if ($_request) {
            return $_request;
        }

        $query = uniqid();
        $_request = Request::create([
            'method' => 'PUT',
            'table_id' => $table->id,
            'query' => $query,
            'payload' => json_encode(static::Payload),
        ]);
        return $_request;
    }

    public function makeDestroyEp(Table $table, $request)
    {
        $requestBody = $request->validated();
        $_request = Request::where('method', 'DESTROY')->where('table_id', $table->id)->first();
        if ($_request) {
            return $_request;
        }

        $query = uniqid();
        $_request = Request::create([
            'method' => "DESTROY",
            'table_id' => $table->id,
            'query' => $query,
        ]);
        return $_request;
    }
}
