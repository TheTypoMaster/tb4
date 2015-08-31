<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 17/07/2015
 * Time: 12:37 PM
 */

namespace TopBetta\Resources;


use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedEloquentResourceCollection implements ResourceCollectionInterface {

    private $collection;

    private $total;

    private $perPage;

    private $lastPage;

    private $currentPage;

    /**
     * @param $collection LengthAwarePaginator
     * @param $class
     */
    public function __construct($collection, $class)
    {
        $this->collection = new EloquentResourceCollection($collection->getCollection(), $class);
        $this->total = $collection->total();
        $this->perPage = $collection->perPage();
        $this->lastPage = $collection->lastPage();
        $this->currentPage = $collection->currentPage();
    }

    public function toArray()
    {
        return array(
            "data" => $this->collection->toArray(),
            "total" => $this->total,
            "per_page" => $this->perPage,
            "last_page" => $this->lastPage,
            "current_page" => $this->currentPage
        );
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray());
    }

    public function getIterator()
    {
        return $this->collection->getIterator();
    }

    public function first()
    {
        return $this->collection->first();
    }

    public function count()
    {
        return $this->collection->count();
    }

}