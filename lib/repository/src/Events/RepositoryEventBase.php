<?php
namespace Fabrikod\Repository\Events;

use Illuminate\Database\Eloquent\Model;
use Fabrikod\Repository\Contracts\Repository;

/**
 * Class RepositoryEventBase
 * @package Fabrikod\Repository\Events

 */
abstract class RepositoryEventBase
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $action;

    /**
     * @param Repository $repository
     * @param Model               $model
     */
    public function __construct(Repository $repository, Model $model = null)
    {
        $this->repository = $repository;
        $this->model = $model;
    }

    /**
     * @return Model|array
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
