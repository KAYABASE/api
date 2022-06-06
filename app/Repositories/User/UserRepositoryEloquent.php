<?php

namespace App\Repositories\User;

use App\Models\Product\Product;
use App\Models\User;
use Fabrikod\Repository\Eloquent\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class UserRepositoryEloquent extends BaseRepository implements UserRepository
{
    /**
     * @inheritDoc
     */
    public function query(): Builder
    {
        return User::query();
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        //
    }

    public function create(array $attributes)
    {
        /** @var User $model */
        $model = parent::create($attributes);

        if (isset($attributes['roles'])) {
            $model->assignRole($attributes['roles']);
        }

        return $model;
    }

    public function update(array $attributes, $id)
    {
        /** @var User $model */
        $model = parent::update($attributes, $id);

        $user = $this->app['auth']->user();

        // If the current user is not trying to change their own roles, it syncs the roles.
        if (isset($attributes['roles']) && $user->id != $id) {
            $model->syncRoles($attributes['roles']);
        }

        return $model;
    }

    public function userProfile()
    {
        return auth()->user();
    }

    public function userOrderList()
    {
        /** @var User $user */

        $user = auth()->user();

        return $user->orders()->paginate(12);
    }

    public function userOrderDetail($id)
    {
        /** @var User $user */

        $user = auth()->user();

        return $user->orders()->find($id);
    }

    public function userFavoriteList()
    {
        /** @var User $user */

        $user = auth()->user();

        return $user->getFavoriteItems(Product::class)->paginate(12);
    }

    public function userCouponList()
    {
        /** @var User $user */

        $user = auth()->user();

        return $user->coupons()->paginate(12);
    }

    public function userCouponDetail($id)
    {
        /** @var User $user */

        $user = auth()->user();

        return $user->coupons()->find($id);
    }
}
