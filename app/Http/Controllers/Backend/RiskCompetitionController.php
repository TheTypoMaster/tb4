<?php

namespace TopBetta\backend;

use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Risk\RiskCompetitionService;

class RiskCompetitionController extends \BaseController {

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

}
