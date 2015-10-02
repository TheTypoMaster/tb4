<?php namespace TopBetta\Http\Controllers\Backend;

use TopBetta\Http\Controllers\Controller;

use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Risk\RiskCompetitionService;

class RiskCompetitionController extends Controller {

    /**
     * @var RiskCompetitionService
     */
    private $competitionService;
    /**
     * @var ApiResponse
     */
    private $apiResponse;

    public function __construct(RiskCompetitionService $competitionService, ApiResponse $apiResponse)
    {
        $this->competitionService = $competitionService;
        $this->apiResponse = $apiResponse;
    }

    public function showCompetition($competition)
    {

        try {
            $competition = $this->competitionService->showCompetition($competition);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success($this->formatCompetitionPayload($competition));
    }

    public function hideCompetition($competition)
    {
        try {
            $competition = $this->competitionService->hideCompetition($competition);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success($this->formatCompetitionPayload($competition));
    }

    private function formatCompetitionPayload($competition)
    {
        $competitionArray = $competition->toArray();

        //need to do this since events is already an attribute of Eloquent models
        $competitionArray['races'] = array();
        foreach($competition->events()->get() as $event) {
            $competitionArray['races'][] = $event->toArray();
        }

        return $competitionArray;
    }

    public function enableFixedOdds($competition)
    {

        try {
            $competition = $this->competitionService->enableFixedOdds($competition);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success($this->formatCompetitionPayload($competition));
    }

    public function disableFixedOdds($competition)
    {
        try {
            $competition = $this->competitionService->disableFixedOdds($competition);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success($this->formatCompetitionPayload($competition));
    }

    public function disableAllFixedOdds()
    {
        try {
            $this->competitionService->disableAllFixedOdds();
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success('OK');
    }

}
