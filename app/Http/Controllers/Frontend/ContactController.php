<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 2/09/2015
 * Time: 8:50 AM
 */

namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;
use Illuminate\Http\Request;
use TopBetta\Services\Response\ApiResponse;

class ContactController extends Controller {

    /**
     * @var ApiResponse
     */
    private $apiResponse;

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function contactUs(Request $request)
    {
        return $this->apiResponse->success("Saved");
    }
}