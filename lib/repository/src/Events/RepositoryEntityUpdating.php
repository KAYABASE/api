<?php
namespace Fabrikod\Repository\Events;

/**
 * Class RepositoryEntityUpdated
 * @package Fabrikod\Repository\Events

 */
class RepositoryEntityUpdating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "updating";
}
