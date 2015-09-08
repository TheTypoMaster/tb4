<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:27 AM
 */

namespace TopBetta\Resources;


use TopBetta\Repositories\Contracts\BetTypeRepositoryInterface;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Services\Betting\EventService;

class RaceResource extends AbstractEloquentResource {

    protected static $modelClass = 'TopBetta\Models\EventModel';

    protected $attributes = array(
        "id"                => 'id',
        "name"              => 'name',
        'meetingName'       => 'meetingName',
        "type"              => "type",
        "start_date"        => 'start_date',
        "number"            => 'number',
        "description"       => 'description',
        "class"             => 'class',
        "distance"          => 'distance',
        "status"            => 'eventstatus.keyword',
        "weather"           => 'weather',
        "track_condition"   => 'track_condition',
        "exoticBetsAllowed" => "exoticBetsAllowed",
        "availableProducts" => "availableProducts",
        "displayedResults"  => "displayedResults",
        "displayedExoticResults" => "displayedExoticResults",
    );

    protected $loadIfRelationExists = array(
        'markets.0.selections' => 'selections',
    );

    private $results = array();

    private $exoticResults = array();

    private $resultString = null;

    private $meetingName;

    protected $includeFullResults = true;

    public function selections()
    {
        $collection = $this->collection('selections', 'TopBetta\Resources\SelectionResource', $this->model->markets->first()->selections);

        //inject products into selection so we can set tote types on prices
        if (array_get($this->relations, 'products')) {
            foreach ($collection as $selection) {
                $selection->setProducts($this->relations['products']);
            }
        }

        return $collection;
    }

    public function setSelections($selections)
    {
        $this->relations['selections'] = $selections;

        //inject products into selection so we can set tote types on prices
        if (array_get($this->relations, 'products')) {
            foreach ($this->relations['selections'] as $selection) {
                $selection->setProducts($this->relations['products']);
            }
        }

        return $this;
    }

    public function bets()
    {
        return $this->collection('bets', 'TopBetta\Resources\BetResource', $this->model->bets);
    }

    /**
     * @return mixed
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param mixed $results
     */
    public function setResults($results)
    {
        $this->results = $results;
    }

    /**
     * @return mixed
     */
    public function getExoticResults()
    {
        return $this->exoticResults;
    }

    /**
     * @param mixed $exoticResults
     */
    public function setExoticResults($exoticResults)
    {
        $this->exoticResults = $exoticResults;
    }

    /**
     * @return mixed
     */
    public function getResultString()
    {
        return $this->resultString;
    }

    /**
     * @param mixed $resultString
     */
    public function setResultString($resultString)
    {
        $this->resultString = $resultString;
    }

    public function setProducts($products)
    {
        $this->relations['products'] = $products;

        if ($selections = array_get($this->relations, 'selections')) {
            //inject products into selection so we can set tote types on prices
            foreach ($selections as $selection) {
                $selection->setProducts($this->relations['products']);
            }
        }

        return $this;
    }

	public function exoticBetsAllowed()
    {
        if (!$this->model->exotic_bets_allowed) {
            return ! EventService::isEventInternational($this->model);
        }

        return $this->model->exotic_bets_allowed;
    }

    public function getType()
    {
        return $this->model->type ? : $this->model->competition->first()->type_code;
    }

    public function getDisplayedResults()
    {
        if (!$this->model->displayed_results) {
            $results = $this->filterResultsByProducts($this->getResults());

            return array_values($this->mergeSelectionPositionResults($results));
        }

        return $this->model->displayed_results;
    }

    public function getDisplayedExoticResults()
    {
        if (!$this->model->displayed_exotic_results) {
            return array_values($this->filterResultsByProducts($this->getExoticResults()));
        }

        return $this->model->displayed_exotic_results;

    }

    /**
     * @return mixed
     */
    public function getMeetingName()
    {
        return $this->meetingName;
    }

    protected function filterResultsByProducts($results)
    {
        if (!array_get($this->relations, 'products')) {
            return array();
        }

        $products = array_map(function ($q) {return array('product_id' => $q['product_id'], 'bet_type' => $q['bet_type']);}, $this->relations['products']->toArray());

        return array_filter($results, function ($v) use ($products) {
            $result =  array('product_id' => $v['product_id'], 'bet_type' => $v['bet_type']);
            return in_array($result, $products);
        });

    }

    protected function mergeSelectionPositionResults($results)
    {
        $mergedResults = array();

        //hack for waiting for cache data
        if (count($results) && isset(reset($results)['selection_id'])) {

            foreach ($results as $result) {
                if (!$resultPrice = array_get($mergedResults, $result['selection_id'])) {
                    $resultPrice = array_except($result, array('bet_type', 'dividend'));
                }

                $resultPrice[$result['bet_type'] == BetTypeRepositoryInterface::TYPE_WIN ? 'win_dividend' : 'place_dividend'] = $result['dividend'];
                $mergedResults[$result['selection_id']]                                                                       = $resultPrice;
            }
        }

        return $mergedResults;
    }

    public function getEventstatus()
    {
        return $this->model->eventstatus;
    }

    public function toArray()
    {
        $array = parent::toArray();

        if($this->includeFullResults && ($this->status == EventStatusRepositoryInterface::STATUS_INTERIM ||
            $this->status == EventStatusRepositoryInterface::STATUS_PAYING ||
            $this->status == EventStatusRepositoryInterface::STATUS_PAID)
        ) {
            $array["results"] = $this->getResults();
            $array["exotic_results"] = $this->getExoticResults();
            $array["result_string"] = $this->getResultString();
        }

        return $array;
    }

    public function availableProducts()
    {
        return !is_array($this->model->available_products) ? json_decode($this->model->available_products, true) : $this->model->available_products;
    }

    public function initialize()
    {
        parent::initialize();

        $tempModel = clone $this->model;

        $this->meetingName = $tempModel->competition->first() ? $tempModel->competition->first()->name : null;
    }

    public static function createResourceFromArray($array, $class = null)
    {
        $resource = parent::createResourceFromArray($array);

        $resource->setResults(array_get($array, 'results', array()));
        $resource->setExoticResults(array_get($array, 'exotic_results', array()));
        $resource->setResultString(array_get($array, 'result_string'));

        return $resource;
    }
}