<?php namespace TopBetta\Http\Controllers\Backend;
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 14/04/2015
 * Time: 11:28 AM
 */


use Input;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Feeds\SportsFeedService;
use TopBetta\Services\Response\ApiResponse;

class SportsFeedController extends Controller {

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
        die('x');
        $data = Input::json()->all();

        //log the payload
        \File::append('/tmp/backAPIsportsJSON-' . date('YmdHis'), json_encode($data));

        //process sports data
        $this->sportsFeedService->processSportsFeed($data);

        return $this->apiResponse->success(array('Processed' => 'OK', 200));
    }
}