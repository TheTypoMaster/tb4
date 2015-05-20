<?php namespace TopBetta\Http\Backend\Controllers;

/**
 * Coded by Oliver Shanahan
 * File creation date: 2/04/15
 * File creation time: 18:40
 * Project: tb4
 */

use BaseController;
use Log;
use Input;

use Exception;
use TopBetta\api\backend\Racing\RaceDataProcessingService;
use TopBetta\Services\Response\ApiResponse;


/**
 * Class RacingDataController
 * @package TopBetta\Http\Backend\Controllers
 */
class RacingDataController extends BaseController {

	private $dataprocessing;

	private $response;

	function __construct(RaceDataProcessingService $dataprocessing,
						 ApiResponse $response)
	{
		$this->dataprocessing = $dataprocessing;
		$this->response = $response;
	}


	public function store()
	{
		// get the JSON POST
		$racingJSON = Input::json();
		//try {
			$this->dataprocessing->processRacingData($racingJSON);
	//	}catch(Exception $e){
	//		return $this->response->failed($e->getMessage(), 400, 400, "JSON could not be processed", "Error processing the JSON payload.");
	//	}

		return $this->response->success(array('Processed' => 'OK', 200));
	}



//		$this->dataprocessing->processRacingData($racingJSON);
//		dd();
//		foreach ($racingJSON as $key => $resultsArray) {
//			$result = $this->results->ResultEvents($resultsArray);
//		}
	//	return Response::json(array('error' => $result['error'], 'message' => $result['message']), $result['status_code']);


}