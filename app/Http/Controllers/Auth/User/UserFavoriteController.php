<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Repositories\User\UserRepository;

class UserFavoriteController extends Controller
{
    public function __construct(public UserRepository $repository)
    {
        # code...
    }

    public function index()
    {
        $resource = $this->repository->userFavoriteList();

        return ProductResource::collection($resource);
    }
}
