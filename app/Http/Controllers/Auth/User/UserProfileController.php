<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\User\UserRepository;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function __construct(public UserRepository $repository)
    {
        # code...
    }
    
    public function index()
    {
        return UserResource::make($this->repository->userProfile());
    }
}
