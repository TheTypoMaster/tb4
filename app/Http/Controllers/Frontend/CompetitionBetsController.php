<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use Auth;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Betting\BetService;
use TopBetta\Services\Response\ApiResponse;

class CompetitionBetsController extends Controller
{
    /**
     * @var BetService
     */
    private $betService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(BetService $betService, ApiResponse $response)
    {
        $this->betService = $betService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {
        return $this->response->success(
            $this->betService->getBetsForEventGroup(Auth::user()->id, $id)->toArray()
        );
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
