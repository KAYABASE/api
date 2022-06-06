<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Auth\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Artisan;
use phpseclib3\Crypt\Hash;

class RegisteredUserController extends Controller
{

    /**
     * Handle an incoming registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Services\ApiResponse|int|string
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request)
    {
        $tenant = Tenant::create($request->only(['name']));
        $tenant->run(function () use ($request) {
            Artisan::call('tenants:seed', array('--class' => 'ClientSeeder', '--tenants' => tenant('id')));
            Artisan::call('tenants:seed', array('--class' => 'PermissionSeeder', '--tenants' => tenant('id')));
            // Artisan::call('tenants:seed', array('--class' => 'SettingsSeeder', '--tenants' => tenant('id')));

            Artisan::call('cache:clear');

            $payload = $request->payload();
            $payload["password"] = \Illuminate\Support\Facades\Hash::make($payload["password"]);

            $user = User::create($payload);

            $user->assignRole(Role::SUPER);
            //event(new Registered($user));
        });

        return $this->success(array_merge(compact('tenant')), message: 'Registered')->status(201);
    }
}
