<?php

namespace TopBetta\Http\Controllers\Backend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Products\ProductService;
use TopBetta\Services\Response\ApiResponse;

class ProductController extends Controller
{
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(ProductService $productService, ApiResponse $response)
    {
        $this->productService = $productService;
        $this->response = $response;
    }

    public function setUserProducts(Request $request)
    {
        try {
            $user = $this->productService->setProductsForUser($request->get('user_id'), $request->get('products', array()), $request->get('bet_type'), $request->get('venue_id', 0));
        } catch (\Exception $e) {
            \Log::error('ProductController: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed('Error with message ' . $e->getMessage());
        }

        return $this->response->success("Products updated");
    }

    public function setMeetingProducts(Request $request)
    {
        try {
            $user = $this->productService->setMeetingProductsForBetType($request->get('meeting_id'), $request->get('products', array()), $request->get('bet_type'));
        } catch (\Exception $e) {
            \Log::error('ProductController: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed('Error with message ' . $e->getMessage());
        }

        return $this->response->success("Products updated");
    }
}
