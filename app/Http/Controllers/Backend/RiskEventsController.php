<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Risk\RiskEventService;

class RiskEventsController extends Controller {

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
			$event = $this->eventService->showEvent($event);
		} catch (\Exception $e) {
			return $this->apiResponse->failed($e->getMessage());
		}

		return $this->apiResponse->success($this->formatEventPayload($event));
	}

	public function hideEvent($event)
	{
		try {
			$event = $this->eventService->hideEvent($event);
		} catch (\Exception $e) {
			return $this->apiResponse->failed($e->getMessage());
		}

		return $this->apiResponse->success($this->formatEventPayload($event));
	}

	private function formatEventPayload($event)
	{
		$eventArray = $event->toArray();

		$eventArray['competition'] = $eventArray['competition'][0];

		return $eventArray;
	}


    public function enableFixedOdds($event)
    {
        try {
            $event = $this->eventService->enableFixedOdds($event);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success($this->formatEventPayload($event));
    }

    public function disableFixedOdds($event)
    {
        try {
            $event = $this->eventService->disableFixedOdds($event);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success($this->formatEventPayload($event));
    }



}
