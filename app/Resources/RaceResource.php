<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 9/07/2015
 * Time: 11:27 AM
 */

namespace TopBetta\Resources;


class RaceResource extends AbstractEloquentResource {

    protected $attributes = array(
        "id"              => 'id',
        "name"            => 'name',
        "start_date"      => 'start_date',
        "number"          => 'number',
        "description"     => 'description',
        "class"           => 'class',
        "distance"        => 'distance',
        "status"          => 'eventstatus.name',
        "weather"         => 'weather',
        "track_condition" => 'track_condition',
        "results"         => "results",
        "exoticResults"   => "exoticResults",
        "resultString"    => "resultString",
    );

    protected $loadIfRelationExists = array(
        'markets.0.selections' => 'selections'
    );

    private $results = array();

    private $exoticResults = array();

    private $resultString = null;

    public function selections()
    {
        return $this->collection('selections', 'TopBetta\Resources\SelectionResource', $this->model->markets->first()->selections);
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

}