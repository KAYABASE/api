<?php

namespace App\Http\Controllers;

use App\Filters\QueryFilter\QueryFilter;
use App\Filters\QueryFilter\QueryFilterOptions;
use App\Filters\RelationFilter\RelationFilter;
use App\Filters\RelationFilter\RelationFilterOptions;
use App\Traits\SendsApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\ControllerMiddlewareOptions;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, SendsApiResponse;

    protected array $allowedRelationships = [];

    protected array $defaultRelationships = [];

    /**
     * Apply the middleware to see if the request is authorized.
     *
     * @return ControllerMiddlewareOptions
     */
    protected function panelMiddleware(): ControllerMiddlewareOptions
    {
        return $this->middleware(['auth', 'can:viewPanel']);
    }

    /**
     * Check authorize ability if the given condition is true.
     *
     * @param $boolean
     * @param $ability
     * @param array $arguments
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeIf($boolean, $ability, $arguments = []): void
    {
        if ($boolean) {
            $this->authorize($ability, $arguments);
        }
    }

    /**
     * Check authorize ability if authenticated user can view panel.
     *
     * @param $ability
     * @param array $arguments
     * @return void
     * @throws AuthorizationException
     */
    public function authorizeByAdmin($ability, $arguments = []): void
    {
        $this->authorizeIf(request()->user()?->viewPanel(), $ability, $arguments);
    }

    /**
     * Apply relation filter to repository.
     *
     * @return RelationFilter
     */
    protected function relationFilter(): RelationFilter
    {
        return resolve(RelationFilter::class, [
            'options' => $this->relationFilterOptions()
        ])->with($this->defaultRelationships);
    }

    /**
     * Apply query filter to repository.
     *
     * @return QueryFilter
     */
    protected function queryFilter(): QueryFilter
    {
        return resolve(QueryFilter::class, [
            'options' => $this->queryFilterOptions()
        ]);
    }

    /**
     * Get the query filter options.
     *
     * @return QueryFilterOptions
     */
    protected function queryFilterOptions()
    {
        return QueryFilterOptions::make();
    }

    /**
     * Get the relation filter options.
     *
     * @return RelationFilterOptions
     */
    protected function relationFilterOptions()
    {
        return RelationFilterOptions::make()->relationshipFilters($this->allowedRelationships);
    }
}
