<?php

namespace TopBetta\Http\Controllers\Backend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\SelectionPriceRepositoryInterface;
use TopBetta\Services\Prices\SelectionPricesService;
use TopBetta\Services\Response\ApiResponse;

class PricesController extends Controller
{
    /**
     * @var SelectionPricesService
     */
    private $pricesService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(SelectionPricesService $pricesService, ApiResponse $response)
    {
        $this->pricesService = $pricesService;
        $this->response      = $response;
    }

    public function override(Request $request)
    {
        \Log::info($request->all());
        try {
            $this->pricesService->overridePrice($request->get('selection_id'), $request->get('product'), $request->get('amount'), $request->get('bet_type'), $request->get('manual', false));
        } catch (\Exception $e) {
            \Log::error("PricesController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed('Error with message ' . $e->getMessage());
        }

        return $this->response->success('Price Updated');
    }
}
