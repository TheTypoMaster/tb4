<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Accounting\AccountTransactionService;
use TopBetta\Services\Resources\Accounting\AccountTransactionResourceService;
use TopBetta\Services\Response\ApiResponse;

class AccountTransactionController extends Controller
{
    const LOG_PRFIX = 'AccountTransactionController';

    /**
     * @var AccountTransactionResourceService
     */
    private $accountTransactionService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(AccountTransactionService $accountTransactionService, ApiResponse $response)
    {
        $this->accountTransactionService = $accountTransactionService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $transactions = $this->accountTransactionService->getUserTransactions(\Auth::user(), $request->get('type', 'all'), $request->get('order', null));
            $transactions = $transactions->toArray();
        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error(self::LOG_PRFIX . ':' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success($transactions['data'], 200, array_except($transactions, 'data'));
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
    public function store()
    {
        //
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
