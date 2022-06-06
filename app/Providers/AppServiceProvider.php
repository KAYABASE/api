<?php

namespace App\Providers;

use App\Models\Product\Product;
use App\Models\User;
use Fabrikod\ApiLocalization\Facades\ApiLocalization;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByRequestData;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class AppServiceProvider extends ServiceProvider
{
    const VERSION = '0.0.1';

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
        Passport::routes(null, ['middleware' => [
            // You can make this simpler by creating a tenancy route group
            InitializeTenancyByRequestData::class,
            PreventAccessFromCentralDomains::class,
        ]]);

        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return 'https://example.com/reset-password?token=' . $token;
        });

        // @TODO - Change real url when webshop is ready.
        VerifyEmail::createUrlUsing(function ($notifiable) {
            return 'https://example.com/verify-email';
        });

        Passport::loadKeysFrom(base_path(config('passport.key_path')));

        $this->requestMacro();
    }

    protected function requestMacro()
    {
        Request::macro('translationRule', function ($name, $required = true) {
            $fallbackLocale = app()->getFallbackLocale();
            $availableLocales = array_keys(Arr::except(ApiLocalization::availableLocales(), [$fallbackLocale]));

            $locales = collect($availableLocales)->mapWithKeys(function ($locale) use ($name) {
                return ["$name.$locale" => ['nullable', 'string']];
            })->all();

            $baseRule = $required ? ['required', 'array'] : ['nullable', 'array'];
            $fallbackRule = $required ? ['required', 'string'] : ['nullable', 'string'];

            return [
                    $name => $baseRule,
                    "$name.$fallbackLocale" => $fallbackRule,
                ] + $locales;
        });
    }
}
