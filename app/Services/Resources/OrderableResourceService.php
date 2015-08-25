<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 27/07/2015
 * Time: 9:51 AM
 */

namespace TopBetta\Services\Resources;


abstract class OrderableResourceService {

    /**
     * @var \TopBetta\Repositories\BaseEloquentRepository
     */
    protected $repository;

    protected $orderFields = array();

    public function setOrder($field, $direction)
    {
        if( $modelField = array_get($this->orderFields, $field) ) {
            $this->repository->setOrder(array($modelField, $direction));
        }
    }
}