<?php

namespace TopBetta\Http\Controllers\Backend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Feeds\Racing\RunnerDataService;
use TopBetta\Services\Response\ApiResponse;
use Log;
use Input;

class TrainerController extends Controller
{
    /**
     * @var RunnerDataService
     */
    private $runnerDataService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(RunnerDataService $runnerDataService, ApiResponse $response)
    {
        $this->runnerDataService = $runnerDataService;
        $this->response = $response;
    }

    public function store()
    {
        try {
            $this->runnerDataService->processTrainer(Input::json()->all());
        } catch (\Exception $e) {
            Log::error("Unknown error processing trainer data " . $e->getMessage() . ' - ' . print_r(Input::json()->all()));
            return $this->response->failed($e->getMessage());
        }

        return $this->response->success(array('Processed' => 'OK', 200));
    }
}
