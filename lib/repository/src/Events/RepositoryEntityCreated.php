<?php
namespace Fabrikod\Repository\Events;

/**
 * Class RepositoryEntityCreated
 * @package Fabrikod\Repository\Events

 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "created";
}
