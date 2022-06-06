<?php
namespace Fabrikod\Repository\Events;

/**
 * Class RepositoryEntityDeleted
 * @package Fabrikod\Repository\Events

 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleted";
}
