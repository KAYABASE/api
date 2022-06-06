<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDeleteRequest;
use App\Http\Requests\Column\ColumnStoreRequest;
use App\Http\Requests\Column\ColumnUpdateRequest;
use App\Http\Resources\ColumnResource;
use App\Models\Column;
use App\Models\Table;
use App\Models\Value;
use App\Repositories\Column\ColumnRepository;
use App\Repositories\Table\TableRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ColumnController extends Controller
{
    protected array $defaultRelationships = ['column', 'column.table'];

    /**
     * @var ColumnRepository
     */
    public function __construct(public ColumnRepository $repository)
    {
        $this->panelMiddleware();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushFilter([
            $this->relationFilter(),
            $this->queryFilter()
        ]);

        $this->authorize('viewAny', Column::class);

        return ColumnResource::collection($this->repository->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Column\ColumnStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Table $table, ColumnStoreRequest $request)
    {
        $requestBody = $request->validated();
        $this->authorize('create', Column::class);

        $column = $this->repository->create(array_merge($requestBody, [
            'table_id' => $table->id
        ]));

        $table->rows()->each(function ($row) use ($column) {
            Value::create([
                'row_id' => $row->id,
                'column_id' => $column->id,
                'value' => null
            ]);
        });

        return ColumnResource::make($column->load($this->defaultRelationships));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view', Column::class);
        $this->repository->pushFilter($this->relationFilter());

        return new ColumnResource($this->repository->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Column\ColumnUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Table $table, ColumnUpdateRequest $request, $id)
    {
        $requestBody = $request->validated();
        $this->authorize('update', Column::class);
        $column = $this->repository->update(
            array_merge($requestBody, ['table_id', $table->id]),
            $id
        );

        return ColumnResource::make($column->load($this->defaultRelationships));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', Column::class);
        $this->repository->findOrFail($id)->delete();

        return response()->json(null, 204);
    }

    /**
     * Bulk delete the specified resource from storage.
     *
     * @param  \App\Http\Requests\BulkDeleteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(BulkDeleteRequest $request)
    {
        $this->authorize('delete', [Database::class, Table::class, Column::class]);
        Column::whereIn('id', $request->ids)->delete();

        return $this->success(message: 'Deleted')->status(204);
    }
}
