<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class UserOrderController extends Controller
{
    public function __construct(public UserRepository $repository)
    {
        # code...
    }

    public function index()
    {
        $resource = $this->repository->userOrderList();

        return UserResource::collection($resource);
    }

    public function show($id)
    {
        $resource = $this->repository->userOrderDetail($id);

        return UserResource::make($resource);
    }
}
