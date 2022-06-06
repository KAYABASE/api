<?php

namespace App\Repositories\User;

use Fabrikod\Repository\Contracts\Repository;

interface UserRepository extends Repository
{
    public function userProfile();

    public function userOrderList();

    public function userOrderDetail($id);

    public function userFavoriteList();

    public function userCouponList();

    public function userCouponDetail($id);

}
