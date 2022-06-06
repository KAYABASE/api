<?php
namespace Fabrikod\Repository\Events;

/**
 * Class RepositoryEntityDeleted
 * @package Fabrikod\Repository\Events

 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleting";
}
