<?php

namespace App\Http\Controllers;

use App\Http\Requests\Request\RequestStoreRequest;
use App\Http\Requests\Request\RequestUpdateRequest;
use App\Http\Requests\Row\RowStoreRequest;
use App\Http\Resources\RequestResource;
use App\Models\Column;
use App\Models\Request;
use App\Models\Row;
use App\Models\Table;
use App\Models\Value;
use App\Repositories\Request\RequestRepository;

class RequestController extends Controller
{
    public function __construct(public RequestRepository $repository)
    {
        $this->panelMiddleware();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($query)
    {
        $request = Request::where('query', $query)->where('method', 'GET')->firstOrFail();
        $column_ids = json_decode($request->filter, true);
        $rows = $request->table->rows()->get();

        foreach ($rows as $row) {
            $row->values = Value::whereIn('column_id', $column_ids)->where('row_id', $row->id)->get();
        }
        return $rows;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($query, RowStoreRequest $request)
    {
        $requestBody = $request->validated();
        $request = Request::where('query', $query)->where('method', 'POST')->firstOrFail();
        
        $row = Row::create([
            'table_id' => $request->table->id
        ]);

        foreach ($requestBody['values'] as $value) {
            Value::create([
                'row_id' => $row->id,
                'column_id' => $value['column_id'],
                'value' => $value['value']
            ]);
        }

        $request = $this->repository->create(array_merge($requestBody, [
            'query' => $query,
            'method' => 'GET'
        ]));

        return new RequestResource($request);
    }
    
    public function request(Table $table, RequestStoreRequest $request)
    {
        $requestBody = $request->validated();
        switch ($requestBody['method']) {
            case 'GET':
                $show = $this->repository->makeShowEp($table, $request);
                return new RequestResource($show);
            case 'POST':
                $store = $this->repository->makeStoreEp($table, $request);
                return new RequestResource($store);
            case 'PUT':
                $update = $this->repository->makeUpdateEp($table, $request);
                return new RequestResource($update);
            case 'DESTROY':
                $destroy = $this->repository->makeDestroyEp($table, $request);
                return new RequestResource($destroy);
            default:
                return response()->json(['message' => 'Method not allowed'], 405);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Request::class);
        $this->repository->findOrFail($id)->delete();

        return response()->json(null, 204);
    }
}
