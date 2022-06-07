<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDeleteRequest;
use App\Http\Requests\Table\TableStoreRequest;
use App\Http\Requests\Table\TableUpdateRequest;
use App\Http\Resources\TableResource;
use App\Models\Column;
use App\Models\Table;
use App\Repositories\Column\ColumnRepository;
use App\Repositories\Table\TableRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    protected array $defaultRelationships = ['database', 'columns', 'rows', 'rows.values', 'rows.values.column'];

    /**
     * @var TableRepository
     * @var ColumnRepository
     */
    public function __construct(
        public TableRepository $repository,
        public ColumnRepository $columnRepository
    ) {
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

        $this->authorize('viewAny', Table::class);

        return TableResource::collection($this->repository->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Table\TableStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TableStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $requestBody = $request->validated();
            $this->authorize('create', [Table::class, Column::class]);

            $table = $this->repository->create(Arr::only($requestBody, ['name', 'database_id']));
            if (@$requestBody['columns'] && count(@$requestBody['columns']) > 0) {
                $this->columnRepository->createMany($table->id, Arr::only($requestBody, ['columns']));
            }

            return TableResource::make($table->load($this->defaultRelationships));
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->authorize('view', [Table::class, Column::class]);
        $this->repository->pushFilter($this->relationFilter());

        return new TableResource($this->repository->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Table\TableUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TableUpdateRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $requestBody = $request->validated();
            $this->authorize('update', [Table::class, Column::class]);
            $table = $this->repository->findOrFail($id);

            $table->update(Arr::only($requestBody, ['name']));
            if (@$requestBody['columns'] && count(@$requestBody['columns']) > 0) {
                $this->columnRepository->updateMany($table->id, $requestBody['columns']);
            }

            return TableResource::make($table->load($this->defaultRelationships));
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', [Table::class, Column::class]);
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
        $this->authorize('delete', [Table::class, Column::class]);
        Table::whereIn('id', $request->ids)->delete();

        return $this->success(message: 'Deleted')->status(204);
    }
}
