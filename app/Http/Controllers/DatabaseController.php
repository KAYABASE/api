<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkDeleteRequest;
use App\Http\Requests\Database\DatabaseStoreRequest;
use App\Http\Requests\Database\DatabaseUpdateRequest;
use App\Http\Resources\DatabaseResource;
use App\Models\Database;
use App\Repositories\Column\ColumnRepository;
use App\Repositories\Database\DatabaseRepository;
use App\Repositories\Table\TableRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class DatabaseController extends Controller
{
    protected array $defaultRelationships = ['tables', 'tables.columns', 'tables.rows', 'tables.rows.values', 'tables.rows.values.column'];

    /**
     * @var DatabaseRepository
     * @var TableRepository
     * @var ColumnRepository
     */
    public function __construct(
        public DatabaseRepository $repository,
        public TableRepository $tableRepository,
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

        $this->authorize('viewAny', Database::class);

        return DatabaseResource::collection($this->repository->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Database\DatabaseStoreRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DatabaseStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $requestBody = $request->validated();
            $this->authorize('create', [Database::class, Table::class, Column::class]);

            $database = $this->repository->create(Arr::only($requestBody, ['name']));
            if (@$requestBody['tables'] && count(@$requestBody['tables']) > 0) {
                $this->tableRepository->createManyWithColumns($database->id, Arr::only($requestBody, ['tables']), $this->columnRepository);
            }

            return DatabaseResource::make($database->load($this->defaultRelationships));
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \App\Http\Resources\DatabaseResource
     */
    public function show($id)
    {
        $this->authorize('view', [Database::class, Table::class, Column::class]);
        $this->repository->pushFilter($this->relationFilter());

        return new DatabaseResource($this->repository->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Database\DatabaseUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DatabaseUpdateRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $requestBody = $request->validated();
            $this->authorize('update', [Database::class, Table::class, Column::class]);
            $database = $this->repository->findOrFail($id);

            $database->update(Arr::only($requestBody, ['name']));
            if (@$requestBody['tables'] && count(@$requestBody['tables']) > 0) {
                $this->tableRepository->updateManyWithColumns($database->id, Arr::only($requestBody, ['tables']), $this->columnRepository);
            }

            return DatabaseResource::make($database->load($this->defaultRelationships));
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
        $this->authorize('delete', [Database::class, Table::class, Column::class]);
        $database = $this->repository->findOrFail($id);
        $database->delete();
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
        Database::whereIn('id', $request->ids)->delete();

        return $this->success(message: 'Deleted')->status(204);
    }
}
