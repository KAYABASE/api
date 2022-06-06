<?php

namespace Fabrikod\LaravelPassportCacheToken;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;

class CacheTokenServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(TokenRepository::class, function () {
            return new CacheTokenRepository(
                \config('passport.cache.prefix'),
                \config('passport.cache.expires_in'),
                \config('passport.cache.tags', []),
                \config('passport.cache.store', \config('cache.default'))
            );
        });

        $this->app->extend(ClientRepository::class, function ($repo, $app) {
            $config = $app['config']->get('passport.personal_access_client');

            return new CacheClientRepository(
                $config['id'] ?? null,
                $config['secret'] ?? null,
                \config('passport.cache.prefix'),
                \config('passport.cache.expires_in'),
                \config('passport.cache.tags', []),
                \config('passport.cache.store', \config('cache.default'))
            );
        });
    }
}
