<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddress\AuthUserAddressStoreRequest;
use App\Http\Requests\UserAddress\AuthUserAddressUpdateRequest;
use App\Http\Resources\UserAddressResource;
use App\Repositories\UserAddress\UserAddressRepository;

class UserAddressController extends Controller
{
    public function __construct(public UserAddressRepository $repository)
    {
        # code...
    }

    public function index()
    {
        $addresses = $this->repository->userAddressList();

        return UserAddressResource::collection($addresses);
    }

    public function show($id)
    {
        $address = $this->repository->userAddressDetail($id);

        return UserAddressResource::make($address);
    }

    public function store(AuthUserAddressStoreRequest $request)
    {
        $resource = $this->repository->createUserAddress($request->validated());

        return UserAddressResource::make($resource);
    }

    public function update(AuthUserAddressUpdateRequest $request, $id)
    {
        $resource = $this->repository->updateUserAddress($request->validated(), $id);

        return UserAddressResource::make($resource);
    }

    public function destroy($id)
    {
        $this->repository->deleteUserAddress($id);

        return $this->success(message: 'Deleted')->status(204);
    }
}
