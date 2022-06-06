<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected array $allowedRelationships = ['roles'];

    public function __construct(public UserRepository $repository)
    {
        $this->panelMiddleware();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushFilter(
            $this->relationFilter(),
            [function ($query) use ($request) {
                $query->withoutSuperAdmins()->where('id', '<>', $request->user()->id);
        }]);

        $this->authorize('viewAny', User::class);

        return UserResource::collection($this->repository->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        $resource = $this->repository->create($request->validated());

        return UserResource::make($resource);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->repository->pushFilter($this->relationFilter());
        
        $resource = $this->repository->find($id);

        $this->authorize('view', $resource);

        return new UserResource($resource);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $resource = $this->repository->update($request->validated(), $id);

        return UserResource::make($resource);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $resource = $this->repository->find($id);

        $this->authorize('delete', $resource);

        $this->repository->delete($id);

        return $this->success(message: 'Deleted')->status(204);
    }
}
