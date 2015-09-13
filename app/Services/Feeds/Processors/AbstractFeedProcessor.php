<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:41 AM
 */

namespace TopBetta\Services\Feeds\Processors;


use TopBetta\Services\Feeds\SportsCollectionContainer;

abstract class AbstractFeedProcessor {

    /**
     * @var SportsCollectionContainer
     */
    protected $modelContainer;

    /**
     * processes data
     * @param $data
     * @return void
     */
    abstract public function process($data);

    /**
     * Process each item in the array
     * @param $dataArray
     */
    public function processArray($dataArray)
    {
        $objects = array();

        //\Log::debug('#####', $dataArray);
        foreach($dataArray as $data) {
            $objects[] = $this->process($data);
        }

        return $objects;
    }

    /**
     * @return mixed
     */
    public function getModelContainer()
    {
        return $this->modelContainer;
    }

    /**
     * @param mixed $modelContainer
     * @return $this
     */
    public function setModelContainer($modelContainer)
    {
        $this->modelContainer = $modelContainer;
        return $this;
    }
}