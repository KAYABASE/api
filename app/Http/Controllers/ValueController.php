<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkDeleteRequest;
use App\Http\Requests\Value\ValueStoreRequest;
use App\Http\Requests\Value\ValueUpdateRequest;
use App\Http\Resources\ValueResource;
use App\Models\Column;
use App\Models\Value;
use App\Repositories\Value\ValueRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ValueController extends Controller
{
    public function __construct(public ValueRepository $repository)
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

        $this->authorize('viewAny', Value::class);
        
        return ValueResource::collection($this->repository->customPaginate($this->repository->getParsed(), @request()->query('perPage') ?? 25));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Value\ValueStoreRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(ValueStoreRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $requestBody = $request->validated();

            if (Column::findOrFail($requestBody['column_id'])->auto_increment) {
                $requestBody['value'] = Value::where('column_id', $requestBody['column_id'])->withTrashed()->pluck('value')->max() + 1;
            }
            $this->authorize('create', Value::class);

            $resource = $this->repository->create($requestBody);

            return ValueResource::make($this->repository->getParsed($resource->id));
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
        return new ValueResource($this->repository->getParsed($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Value\ValueUpdateRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ValueUpdateRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $requestBody = $request->validated();
            $this->authorize('update', Value::class);

            $table = $this->repository->update($requestBody, $id);

            return ValueResource::make($table->load($this->defaultRelationships));
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
        $this->authorize('delete', Value::class);
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
        $this->authorize('delete', Value::class);
        Value::whereIn('id', $request->ids)->delete();

        return $this->success(message: 'Deleted')->status(204);
    }
}
