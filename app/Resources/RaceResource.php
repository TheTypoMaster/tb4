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
        "status"            => 'eventstatus.name',
        "weather"           => 'weather',
        "track_condition"   => 'track_condition',
        "exoticBetsAllowed" => "exoticBetsAllowed",
        "availableProducts" => "availableProducts",
        "displayedResults"  => "displayedResults",
        "displayedExoticResults" => "displayedExoticResults",
    );

    protected $loadIfRelationExists = array(
        'markets.0.selections' => 'selections'
    );

    private $results = array();

    private $exoticResults = array();

    private $resultString = null;

    private $meetingName;

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
        return ! EventService::isEventInternational($this->model);
    }

    public function getType()
    {
        return $this->model->competition->first()->type_code;
    }

    public function getDisplayedResults()
    {
        $results = $this->filterResultsByProducts($this->getResults());

        return array_values($this->mergeSelectionPositionResults($results));
    }

    public function getDisplayedExoticResults()
    {
        return array_values($this->filterResultsByProducts($this->getExoticResults()));
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

        if( $this->model->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_INTERIM ||
            $this->model->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAYING ||
            $this->model->eventstatus->keyword == EventStatusRepositoryInterface::STATUS_PAID
        ) {
            $array["results"] = $this->getResults();
            $array["exoticResults"] = $this->getExoticResults();
            $array["resultString"] = $this->getResultString();
        }

        return $array;
    }

    public function availableProducts()
    {
        return json_decode($this->model->available_products, true);
    }

    public function initialize()
    {
        parent::initialize();

        $tempModel = clone $this->model;

        $this->meetingName = $tempModel->competition->first()->name;
    }
}