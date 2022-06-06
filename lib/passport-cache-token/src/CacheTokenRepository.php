<?php

namespace Fabrikod\LaravelPassportCacheToken;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\TokenRepository;

class CacheTokenRepository extends TokenRepository
{
    /**
     * @var string
     */
    protected $cacheKeyPrefix;

    /**
     * @var int
     */
    protected $expiresInSeconds;

    /**
     * @var array
     */
    protected $cacheTags;

    /**
     * @var string
     */
    protected $cacheStore;

    /**
     * @param string|null $cacheKeyPrefix
     * @param int|null    $expiresInSeconds
     * @param array       $tags
     * @param string|null $store
     */
    public function __construct(string $cacheKeyPrefix = null, int $expiresInSeconds = null, array $tags = [], ?string $store = null)
    {
        $this->cacheKeyPrefix = sprintf('%s_token_', $cacheKeyPrefix ?? 'passport');
        $this->expiresInSeconds = $expiresInSeconds ?? 5 * 60;
        $this->cacheTags = $tags;
        $this->cacheStore = $store ?? \config('cache.default');
    }

    /**
     * Get a token by the given ID.
     *
     * @param string $id
     *
     * @return \Laravel\Passport\Token
     */
    public function find($id)
    {
        return $this->store()->remember(
            $this->itemKey($id),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($id) {
                return parent::find($id);
            }
        );
    }

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param string $id
     * @param int    $userId
     *
     * @return \Laravel\Passport\Token|null
     */
    public function findForUser($id, $userId)
    {
        return $this->store()->remember(
            $this->itemKey($id),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($id, $userId) {
                return parent::findForUser($id, $userId);
            }
        );
    }

    /**
     * Get the token instances for the given user ID.
     *
     * @param mixed $userId
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($userId): Collection
    {
        return $this->store()->remember(
            $this->itemKey($userId),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($userId) {
                return parent::forUser($userId);
            }
        );
    }

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param \Laravel\Passport\Client            $client
     *
     * @return \Laravel\Passport\Token|null
     */
    public function getValidToken($user, $client)
    {
        return $this->store()->remember(
            $this->itemKey($user->getKey()),
            \now()->addSeconds($this->expiresInSeconds),
            function () use ($client, $user) {
                return parent::getValidToken($user, $client);
            }
        );
    }

    public function itemKey(string $key)
    {
        return $this->cacheKeyPrefix . $key;
    }

    public function store(): Repository
    {
        $store = Cache::store($this->cacheStore);

        return $store instanceof TaggableStore ? $store->tags($this->cacheTags) : $store;
    }

    public function revokeAccessToken($id)
    {
        parent::revokeAccessToken($id);

        $this->store()->forget($this->itemKey($id));
    }
}
