<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\ContactSettingsPolicy;
use App\Settings\ContactSettings;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Activitylog\Models\Activity;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        ContactSettings::class => ContactSettingsPolicy::class,
        Activity::class => ActivityPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
        }

        Gate::before(function ($user, $ability, $arguments) {
            $model = head($arguments);

            // If the given model is an instance of the User class,
            // we'll check delete state of the user. The current user
            // can't delete himself. So, if the user is the current user,
            // we'll return null to manage it in UserPolicy.
            if ($model instanceof User && $ability == 'delete' && $model->id === $user->id) {
                return null;
            }

            return $user->isSuper() ? true : null;
        });

        Gate::define('viewPanel', function ($user) {
            return $user->viewPanel();
        });
    }
}
