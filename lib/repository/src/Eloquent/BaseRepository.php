<?php

namespace Fabrikod\Repository\Eloquent;

use App\Models\Tenant;
use Closure;
use Fabrikod\Repository\Contracts\CriteriaInterface;
use Fabrikod\Repository\Contracts\Repository;
use Fabrikod\Repository\Contracts\RepositoryCriteriaInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Fabrikod\Repository\Events\RepositoryEntityCreated;
use Fabrikod\Repository\Events\RepositoryEntityCreating;
use Fabrikod\Repository\Events\RepositoryEntityDeleted;
use Fabrikod\Repository\Events\RepositoryEntityDeleting;
use Fabrikod\Repository\Events\RepositoryEntityUpdated;
use Fabrikod\Repository\Events\RepositoryEntityUpdating;
use Fabrikod\Repository\Traits\InteractsWithFilters;
use Fabrikod\Repository\Traits\InteractsWithPagination;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

abstract class BaseRepository implements Repository, RepositoryCriteriaInterface
{
    use InteractsWithFilters, InteractsWithPagination;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var Model|null
     */
    protected $resource;

    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * @var array
     */
    protected $fieldSearchable = [];

    /**
     * @param Application $app
     */
    public function __construct(protected Application $app)
    {
        $tenantId = \request()->header("X-Tenant");
        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            tenancy()->initialize($tenant);
        }

        $this->filters = new Collection;
        $this->query = $this->query();
        $this->criteria = new Collection();
        $this->boot();
    }

    public function boot()
    {

    }

    /**
     * Model eloquent builder
     *
     * @return Builder
     */
    abstract public function query(): Builder;

    /**
     * Reset the query
     *
     * @return $this
     */
    public function resetQuery()
    {
        $this->query = $this->query();

        return $this;
    }

    /**
     * Get query instance
     *
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Returns the current Model instance
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->query->getModel();
    }

    /**
     * Get the resource
     *
     * @return Model|null
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Retrieve data array for populate field select
     * Compatible with Laravel 5.3
     *
     * @param string $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck($column, $key = null)
    {
        $this->applyCriteria();
        return $this->withFilters(fn() => $this->query->pluck($column, $key));
    }

    /**
     * Sync relations
     *
     * @param      $id
     * @param      $relation
     * @param      $attributes
     * @param bool $detaching
     *
     * @return mixed
     */
    public function sync($id, $relation, $attributes, $detaching = true)
    {
        return $this->find($id)->{$relation}()->sync($attributes, $detaching);
    }

    /**
     * SyncWithoutDetaching
     *
     * @param $id
     * @param $relation
     * @param $attributes
     *
     * @return mixed
     */
    public function syncWithoutDetaching($id, $relation, $attributes)
    {
        return $this->sync($id, $relation, $attributes, false);
    }

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();
        return $this->withFilters(fn() => $this->query->get($columns));
    }

    /**
     * Count results of repository
     *
     * @param array $where
     * @param string $columns
     *
     * @return int
     */
    public function count($columns = '*')
    {
        $this->applyCriteria();

        return $this->withFilters(fn() => $this->query->count($columns));
    }

    /**
     * Alias of All method
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function get($columns = ['*'])
    {
        return $this->all($columns);
    }

    /**
     * Retrieve first data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function first($columns = ['*'])
    {
        $this->applyCriteria();

        return $this->withFilters(fn() => $this->query->first($columns));
    }

    /**
     * Retrieve first data of repository, or return new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function firstOrNew(array $attributes = [])
    {
        $this->applyCriteria();

        return $this->withFilters(fn() => $this->query->firstOrNew($attributes));
    }

    /**
     * Retrieve first data of repository, or create new Entity
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function firstOrCreate(array $attributes = [])
    {
        $this->applyCriteria();

        return $this->withFilters(fn() => $this->query->firstOrCreate($attributes));
    }

    /**
     * Retrieve data of repository with limit applied
     *
     * @param int $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function limit($limit, $columns = ['*'])
    {
        $this->take($limit);

        return $this->all($columns);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function take($limit)
    {
        return $this->query->limit($limit);
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null|int $limit
     * @param array $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($columns = ['*'], $method = "paginate")
    {
        $this->applyCriteria();

        return $this->withFilters(function () use ($columns, $method) {
            return $this->pagination($this->query, $method, $columns);
        });
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null|int $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate($columns = ['*'])
    {
        return $this->paginate($columns, "simplePaginate");
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'], bool $force = true)
    {
        $this->applyCriteria();

        if (!$force && $this->resource) {
            return $this->resource;
        }

        return $this->withFilters(fn() => $this->resource = $this->query->findOrFail($id, $columns));
    }

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     *
     * @return mixed
     * @throws ValidatorException
     *
     */
    public function create(array $attributes)
    {
        event(new RepositoryEntityCreating($this, $attributes));

        $model = $this->query->create($attributes);

        event(new RepositoryEntityCreated($this, $model));

        return $model;
    }

    /**
     * Update a entity in repository by id
     *
     * @param array $attributes
     * @param       $id
     *
     * @return mixed
     * @throws ValidatorException
     *
     */
    public function update(array $attributes, $id)
    {
        $model = $this->find($id, force: false);

        event(new RepositoryEntityUpdating($this, $model));

        $model->fill($attributes);

        $model->save();

        event(new RepositoryEntityUpdated($this, $model));

        return $model;
    }

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     *
     * @return mixed
     * @throws ValidatorException
     *
     */
    public function updateOrCreate(array $attributes, array $values = [])
    {
        event(new RepositoryEntityCreating($this, $attributes));

        $model = $this->query->updateOrCreate($attributes, $values);

        event(new RepositoryEntityUpdated($this, $model));

        return $model;
    }

    /**
     * Delete a entity in repository by id
     *
     * @param $id
     *
     * @return int
     */
    public function delete($id)
    {
        $model = $this->find($id, force: false);

        $originalModel = clone $model;

        event(new RepositoryEntityDeleting($this, $model));

        $deleted = $model->delete();

        event(new RepositoryEntityDeleted($this, $originalModel));

        return $deleted;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->query->has($relation);

        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $relations = is_string($relations) ? func_get_args() : $relations;

        $this->query->with($relations);

        return $this;
    }

    /**
     * Add subselect queries to count the relations.
     *
     * @param mixed $relations
     *
     * @return $this
     */
    public function withCount($relations)
    {
        $this->query->withCount($relations);

        return $this;
    }

    /**
     * Load relation with closure
     *
     * @param string $relation
     * @param closure $closure
     *
     * @return $this
     */
    public function whereHas($relation, $closure)
    {
        $this->query->whereHas($relation, $closure);

        return $this;
    }

    /**
     * Set the "orderBy" value of the query.
     *
     * @param mixed $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->query->orderBy($column, $direction);

        return $this;
    }

    public function latest(?string $column = null)
    {
        $this->query->latest($column);

        return $this;
    }

    /**
     * Push Criteria for filter the query
     *
     * @param $criteria
     *
     * @return $this
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaInterface) {
            throw new RepositoryException("Class " . get_class($criteria) . " must be an instance of Prettus\\Repository\\Contracts\\CriteriaInterface");
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria)
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Find data by Criteria
     *
     * @param CriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaInterface $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        $results = $this->model->get();
        $this->resetModel();

        return $this->parserResult($results);
    }

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;

        return $this;
    }

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria()
    {
        if ($this->skipCriteria === true) {
            return $this;
        }

        $orderBy = request()->get(config('repository.criteria.params.orderBy', 'orderBy'), null);
        $sortedBy = request()->get(config('repository.criteria.params.sortedBy', 'sortedBy'), 'asc');
        $sortedBy = !empty($sortedBy) ? $sortedBy : 'asc';

        if (isset($orderBy) && !empty($orderBy)) {
            $orderBySplit = explode(';', $orderBy);

            if (count($orderBySplit) > 1) {
                $sortedBySplit = explode(';', $sortedBy);
                foreach ($orderBySplit as $orderBySplitItemKey => $orderBySplitItem) {
                    $sortedBy = isset($sortedBySplit[$orderBySplitItemKey]) ? $sortedBySplit[$orderBySplitItemKey] : $sortedBySplit[0];
                    $this->parserFieldsOrderBy($this->query->getModel(), $orderBySplitItem, $sortedBy);
                }
            } else {
                $this->parserFieldsOrderBy($this->query->getModel(), $orderBySplit[0], $sortedBy);
            }
        }

        return $this;
    }

    /**
     * @param $model
     * @param $orderBy
     * @param $sortedBy
     * @return mixed
     */
    protected function parserFieldsOrderBy($model, $orderBy, $sortedBy)
    {
        $split = explode('|', $orderBy);
        if (count($split) > 1) {
            /*
             * ex.
             * products|description -> join products on current_table.product_id = products.id order by description
             *
             * products:custom_id|products.description -> join products on current_table.custom_id = products.id order
             * by products.description (in case both tables have same column name)
             */
            $table = $model->getModel()->getTable();
            $sortTable = $split[0];
            $sortColumn = $split[1];

            $split = explode(':', $sortTable);
            $localKey = '.id';
            if (count($split) > 1) {
                $sortTable = $split[0];

                $commaExp = explode(',', $split[1]);
                $keyName = $table . '.' . $split[1];
                if (count($commaExp) > 1) {
                    $keyName = $table . '.' . $commaExp[0];
                    $localKey = '.' . $commaExp[1];
                }
            } else {
                /*
                 * If you do not define which column to use as a joining column on current table, it will
                 * use a singular of a join table appended with _id
                 *
                 * ex.
                 * products -> product_id
                 */
                $prefix = Str::singular($sortTable);
                $keyName = $table . '.' . $prefix . '_id';
            }

            $this->query
                ->leftJoin($sortTable, $keyName, '=', $sortTable . $localKey)
                ->orderBy($sortColumn, $sortedBy)
                ->addSelect($table . '.*');
        } else {
            $this->query->orderBy($orderBy, $sortedBy);
        }
        return $this->query;
    }

    /**
     * Get Searchable Fields
     *
     * @return array
     */
    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }


    /**
     * Trigger method calls to the model
     *
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        return $this->withFilters(fn() => call_user_func_array([$this->query, $method], $arguments));
    }
}
