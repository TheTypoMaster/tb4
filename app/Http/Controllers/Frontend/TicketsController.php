<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use Auth;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Services\Resources\Tournaments\TicketResourceService;
use TopBetta\Services\Response\ApiResponse;

class TicketsController extends Controller
{
    /**
     * @var TicketResourceService
     */
    private $ticketResourceService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(TicketResourceService $ticketResourceService, ApiResponse $response)
    {
        $this->ticketResourceService = $ticketResourceService;
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

    public function getRecentAndActiveTicketsForUser()
    {
        return $this->response->success(
            $this->ticketResourceService->getRecentAndActiveTicketsForUser(Auth::user()->id)->toArray()
        );
    }

    public function nextToJump()
    {
        return $this->response->success(
            $this->ticketResourceService->nextToJumpTicketsForUser(Auth::user()->id)->toArray()
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
