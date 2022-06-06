<?php

namespace App\Http\Controllers\Auth\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserCart\AddCartProductRequest;
use App\Http\Requests\UserCart\RemoveCartProductRequest;
use App\Http\Requests\UserCart\UpdateCartRequest;
use App\Http\Resources\User\UserCartResource;
use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use App\Repositories\Cart\CartRepository;

class UserCartController extends Controller
{
    public function __construct(public CartRepository $repository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserCartResource::make($this->repository->byCurrentUser());
    }

    /**
     * Update the user cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCartRequest $request)
    {
        $cart = $this->repository->updateByCurrentUser($request->validated());

        return UserCartResource::make($cart);
    }

    /**
     * Add a product to cart.
     *
     * @param AddCartProductRequest  $request
     *
     * @return UserCartResource
     */
    public function addProduct(AddCartProductRequest $request)
    {
        $cart = $this->repository->addProduct($request->guessProduct());

        return UserCartResource::make($cart);
    }

    /**
     * Remove a product from cart.
     *
     * @param RemoveCartProductRequest $request
     *
     * @return UserCartResource
     */
    public function removeProduct(RemoveCartProductRequest $request)
    {
        $cart = $this->repository->removeProduct($request->guessProduct());

        return UserCartResource::make($cart);
    }

    /**
     * Remove all products from cart.
     *
     * @return UserCartResource
     */
    public function clear()
    {
        $cart = $this->repository->clear();

        return UserCartResource::make($cart);
    }
}
