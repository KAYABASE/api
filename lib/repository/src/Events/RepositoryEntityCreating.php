<?php

namespace Fabrikod\Repository\Events;

use Illuminate\Database\Eloquent\Model;
use Fabrikod\Repository\Contracts\Repository;

/**
 * Class RepositoryEntityCreated
 *
 * @package Fabrikod\Repository\Events

 */
class RepositoryEntityCreating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "creating";

    public function __construct(Repository $repository, array $model)
    {
        parent::__construct($repository);
        $this->model = $model;
    }
}
