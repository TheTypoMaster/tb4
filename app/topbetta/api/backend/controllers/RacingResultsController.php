<?php namespace TopBetta\backend;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 16:43
 * Project: tb4
 */

use BaseController;
use Input;
use Response;
use TopBetta\api\backend\Racing\RaceResulting;

class RacingResultsController extends BaseController {
    protected $restults;

    public function __construct(RaceResulting $results){
        $this->results = $results;
    }

    public function store()
    {
        // get the JSON POST
        $racingJSON = Input::json();

        foreach ($racingJSON as $key => $resultsArray) {
            $result = $this->results->ResultEvents($resultsArray);
        }

        return Response::json($result, 400);
    }
}