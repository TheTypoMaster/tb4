<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:28 AM
 */

namespace TopBetta\backend;

use Input;
use TopBetta\Services\Feeds\SportsFeedService;
use TopBetta\Services\Response\ApiResponse;

class SportsFeedController extends \BaseController {

    /**
     * @var SportsFeedService
     */
    private $sportsFeedService;
    /**
     * @var ApiResponse
     */
    private $apiResponse;

    public function __construct(SportsFeedService $sportsFeedService, ApiResponse $apiResponse)
    {
        $this->sportsFeedService = $sportsFeedService;
        $this->apiResponse = $apiResponse;
    }

    public function store()
    {
        $data = Input::json()->all();

        //log the payload
        \File::append('/tmp/backAPIsportsJSON-' . date('YmdHis'), json_encode($data));

        //process sports data
        $this->sportsFeedService->processSportsFeed($data);

        return $this->apiResponse->success(array('Processed' => 'OK', 200));
    }
}