<?php

namespace App\Policies;

use App\Models\Auth\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(User $user, $ability, $model = null)
    {
        if ($model instanceof User && $model->isSuper()) {
            return false;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny($user)
    {
        return $user->hasAnyPermission(['view user', 'create user', 'update user', 'delete user']);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Courier  $courier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view($user, User $model)
    {
        return $user->hasPermissionTo('view user');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create($user)
    {
        return $user->hasPermissionTo('create user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Courier  $courier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update($user, User $model)
    {
        // A user can update their own profile.
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo('update user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Courier  $courier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete($user, User $model)
    {
        // A user cant delete himself
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermissionTo('delete user');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Courier  $courier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Courier  $courier
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
