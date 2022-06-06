<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDeleteRequest;
use App\Http\Requests\Row\RowStoreRequest;
use App\Http\Requests\Row\RowUpdateRequest;
use App\Http\Resources\RowResource;
use App\Models\Row;
use App\Models\Table;
use App\Models\Value;
use App\Repositories\Row\RowRepository;
use App\Repositories\Value\ValueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RowController extends Controller
{
    protected array $defaultRelationships = ['values', 'values.column'];

    public function __construct(public RowRepository $repository, public ValueRepository $valueRepository)
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

        $this->authorize('viewAny', Row::class);

        return RowResource::collection($this->repository->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Table $table, RowStoreRequest $request)
    {
        return DB::transaction(function () use ($table, $request) {
            $requestBody = $request->validated();
            $resource = $table->rows()->create($requestBody);

            foreach ($requestBody['values'] as $item) {
                Value::create(array_merge($item, [
                    'row_id' => $resource->id,
                ]));
            }

            foreach ($table->columns()->get() as $column) {
                if (Value::where('column_id', $column->id)->where('row_id', $resource->id)->count() === 0) {
                    Value::create([
                        'row_id' => $resource->id,
                        'column_id' => $column->id,
                        'value' => null
                    ]);
                }
            }

            return RowResource::make($resource->load($this->defaultRelationships));
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
        $this->authorize('view', Row::class);
        $this->repository->pushFilter($this->relationFilter());

        return new RowResource($this->repository->findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Table $table, RowUpdateRequest $request, $id)
    {
        $requestBody = $request->validated();
        $resource = $this->repository->update($requestBody, $id);

        if (@$requestBody['values'] && count(@$requestBody['values']) > 0) {
            $this->valueRepository->updateMany($requestBody['values']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Table $table, $id)
    {
        $this->authorize('delete', Row::class);
        $row = $this->repository->findOrFail($id);
        $row->values()->delete();
        $row->delete();

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
        $this->authorize('delete', Row::class);
        Row::whereIn('id', $request->ids)->delete();

        return $this->success(message: 'Deleted')->status(204);
    }
}
