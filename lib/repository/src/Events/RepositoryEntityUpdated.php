<?php
namespace Fabrikod\Repository\Events;

/**
 * Class RepositoryEntityUpdated
 * @package Fabrikod\Repository\Events

 */
class RepositoryEntityUpdated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "updated";
}
