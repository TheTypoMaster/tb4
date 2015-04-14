<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:41 AM
 */

namespace TopBetta\Services\Feeds\Processors;


abstract class AbstractFeedProcessor {

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
        foreach($dataArray as $data) {
            $this->process($data);
        }
    }
}