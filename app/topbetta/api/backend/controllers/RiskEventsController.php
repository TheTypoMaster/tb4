<?php

namespace TopBetta\backend;

use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Risk\RiskEventService;

class RiskEventsController extends \BaseController {

	/**
	 * @var RiskEventService
	 */
	private $eventService;
	/**
	 * @var ApiResponse
	 */
	private $apiResponse;

	public function __construct(RiskEventService $eventService, ApiResponse $apiResponse)
	{
		$this->eventService = $eventService;
		$this->apiResponse = $apiResponse;
	}

	public function showEvent($event)
	{
		try {
			$this->eventService->showEvent($event);
		} catch (\Exception $e) {
			return $this->apiResponse->failed($e->getMessage());
		}

		return $this->apiResponse->success(array());
	}

	public function hideEvent($event)
	{
		try {
			$this->eventService->hideEvent($event);
		} catch (\Exception $e) {
			return $this->apiResponse->failed($e->getMessage());
		}

		return $this->apiResponse->success(array());
	}



}
