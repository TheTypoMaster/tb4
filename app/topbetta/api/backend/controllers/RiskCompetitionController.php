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
            $this->competitionService->showCompetition($competition);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success(array());
    }

    public function hideCompetition($competition)
    {
        try {
            $this->competitionService->hideCompetition($competition);
        } catch (\Exception $e) {
            return $this->apiResponse->failed($e->getMessage());
        }

        return $this->apiResponse->success(array());
    }

}
