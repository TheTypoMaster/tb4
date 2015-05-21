<?php namespace TopBetta\Http\Controllers\Backend;
/**
 * Coded by Oliver Shanahan
 * File creation date: 1/12/14
 * File creation time: 16:43
 * Project: tb4
 */

use TopBetta\Http\Controllers\Controller;
use Input;
use Response;
use TopBetta\api\backend\Racing\RaceResulting;

class RacingResultsController extends Controller {
    protected $results;

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
        return Response::json(array('error' => $result['error'], 'message' => $result['message']), $result['status_code']);
    }
}