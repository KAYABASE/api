<?php
namespace Fabrikod\Repository\Contracts;

/**
 * Interface CriteriaInterface
 * @package Prettus\Repository\Contracts
 * @author Anderson Andrade <contato@andersonandra.de>
 */
interface CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param Repository $repository
     *
     * @return mixed
     */
    public function apply($model, Repository $repository);
}
