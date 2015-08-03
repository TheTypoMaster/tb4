<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Accounting\WithdrawalService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class WithdrawalController extends Controller
{

    /**
     * @var WithdrawalService
     */
    private $withdrawalService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(WithdrawalService $withdrawalService, ApiResponse $response)
    {
        $this->withdrawalService = $withdrawalService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($type, Request $request)
    {
        try {
            $withdrawal = $this->withdrawalService->processWithdrawalRequest(\Auth::user(), $request->all(), $type);
        } catch (ValidationException $e) {
            return $this->response->failed($e->getErrors(), 400);
        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error("WithdrawalController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success($withdrawal->toArray());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
