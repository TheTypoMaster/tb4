<?php namespace TopBetta\api\backend;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 16:43
 * Project: tb4
 */

use Input;
use TopBetta\api\backend\Racing\RaceResulting;

class RacingResultsController
{

    protected $restults;

    public function __construct(RaceResulting $results){
        $this->results = $results;
    }

    public function store()
    {
        // get the JSON POST
        $racingJSON = \Input::json();

        foreach ($racingJSON as $key => $resultsArray) {
            if (is_array($resultsArray) && $key == 'ResultList') {

                $this->results->ResultEvents($resultsArray);


            }
        }
    }
}