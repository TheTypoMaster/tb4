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
    public function __construct($collection = null, $class = null)
    {
        if ($collection) {
            $this->collection = new EloquentResourceCollection($collection->getCollection(), $class);
            $this->total = $collection->total();
            $this->perPage = $collection->perPage();
            $this->lastPage = $collection->lastPage();
            $this->currentPage = $collection->currentPage();
        }
    }

    public static function makeFromEloquentResourceCollection($collection, $limit, $offset)
    {
        $paginatedCollection = new static;

        if (!$collection->slice($offset * $limit, $limit)) {
            throw new \InvalidArgumentException("Invalid page");
        }

        $paginatedCollection->setCollection($collection->slice($offset * $limit, $limit));
        $paginatedCollection->setCurrentPage($offset)
            ->setPerPage($limit)
            ->setTotal($collection->count())
            ->setLastPage(ceil($collection->count() / $limit));

        return $paginatedCollection;
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

    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param EloquentResourceCollection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
        return $this;
    }


    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     * @return $this
     */
    public function setTotal($total)
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getLastPage()
    {
        return $this->lastPage;
    }

    /**
     * @param int $lastPage
     * @return $this
     */
    public function setLastPage($lastPage)
    {
        $this->lastPage = $lastPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
        return $this;
    }



}