<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Auth;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Services\Exceptions\UnauthorizedAccessException;
use TopBetta\Services\Resources\Tournaments\TicketResourceService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\TournamentTicketService;

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
    /**
     * @var TournamentTicketService
     */
    private $ticketService;

    public function __construct(TournamentTicketService $ticketService, TicketResourceService $ticketResourceService, ApiResponse $response)
    {
        $this->ticketResourceService = $ticketResourceService;
        $this->response = $response;
        $this->ticketService = $ticketService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if( $date = $request->get('date') ) {
            return $this->response->success($this->ticketService->getTicketsForUserByDate($user->id, $date)->toArray());
        }

        try {
            $tickets = $this->ticketService->getTicketsForUser($user->id, $request->get('type', 'all'));
        } catch ( \InvalidArgumentException $e ) {
            return $this->response->failed($e->getMessage(), 400);
        }

        $tickets = $tickets->toArray();
        return $this->response->success(array_get($tickets, 'data', $tickets), 200, array_get($tickets, 'data') ? array_except($tickets, 'data') : array());
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
        try {
            $ticket = $this->ticketService->getTicketForUser($id, Auth::user());
        } catch (UnauthorizedAccessException $e) {
            return $this->response->failed("Unauthorized", 401);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed("Ticket does not exist", 404);
        }

        return $this->response->success($ticket->toArray());
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
