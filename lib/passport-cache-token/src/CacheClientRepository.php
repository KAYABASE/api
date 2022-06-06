<?php

namespace Fabrikod\LaravelPassportCacheToken;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Passport\Passport;
use Laravel\Passport\Token;
use Illuminate\Support\Facades\Cache;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\TokenRepository;

class CacheClientRepository extends ClientRepository
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
    public function __construct(
        $personalAccessClientId = null,
        $personalAccessClientSecret = null,
        string $cacheKeyPrefix = null,
        int $expiresInSeconds = null,
        array $tags = [],
        ?string $store = null
    ) {
        parent::__construct($personalAccessClientId, $personalAccessClientSecret);

        $this->cacheKeyPrefix = sprintf('%s_client_', $cacheKeyPrefix ?? 'passport');
        $this->expiresInSeconds = $expiresInSeconds ?? 5 * 60;
        $this->cacheTags = $tags;
        $this->cacheStore = $store ?? \config('cache.default');
    }

    public function find($id)
    {
        return $this->store()->remember(
            $this->itemKey($id),
            now()->addSeconds($this->expiresInSeconds),
            function () use ($id) {
                return parent::find($id);
            }
        );
    }


    public function findForUser($id, $userId)
    {
        return $this->store()->remember(
            $this->itemKey($id),
            now()->addSeconds($this->expiresInSeconds),
            function () use ($id, $userId) {
                return parent::findForUser($id, $userId);
            }
        );
    }


    public function forUser($userId): Collection
    {
        return $this->store()->remember(
            $this->itemKey($userId),
            now()->addSeconds($this->expiresInSeconds),
            function () use ($userId) {
                return parent::forUser($userId);
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

    public function delete(Client $client)
    {
        parent::delete($client);

        $this->store()->forget($this->itemKey($client->getKey()));
    }
}
