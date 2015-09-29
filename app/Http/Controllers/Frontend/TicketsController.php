<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use Auth;
use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Cache\Tournaments\TournamentTicketRepository;
use TopBetta\Repositories\Contracts\TournamentTicketRepositoryInterface;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Resources\Tournaments\TicketResource;
use TopBetta\Services\Betting\Exceptions\BetLimitExceededException;
use TopBetta\Services\Exceptions\UnauthorizedAccessException;
use TopBetta\Services\Resources\Tournaments\TicketResourceService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\Betting\Exceptions\TournamentBetLimitExceededException;
use TopBetta\Services\Tournaments\Exceptions\TournamentBuyInException;
use TopBetta\Services\Tournaments\Exceptions\TournamentEntryException;
use TopBetta\Services\Tournaments\TournamentBuyInService;
use TopBetta\Services\Tournaments\TournamentService;
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
    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var TournamentBuyInService
     */
    private $buyInService;

    public function __construct(TournamentTicketService $ticketService,
                                TicketResourceService $ticketResourceService,
                                TournamentService $tournamentService,
                                TournamentBuyInService $buyInService,
                                ApiResponse $response)
    {
        $this->ticketResourceService = $ticketResourceService;
        $this->response = $response;
        $this->ticketService = $ticketService;
        $this->tournamentService = $tournamentService;
        $this->buyInService = $buyInService;
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
            return $this->response->success($this->ticketService->getTicketsForUserByDate($user->id, $date, $request->get('with'))->toArray());
        }

        try {
            $tickets = $this->ticketService->getTicketsForUser($user->id, $request->get('type', 'all'), $request->get('with'));
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

    public function getTicketForUserInTournament(Request $request, TournamentTicketRepository $ticketRepository)
    {
        if (!$tournament = $request->get('tournament_id')) {
            return $this->response->failed('No tournament_id specified', 400);
        }

        $ticket = $ticketRepository->getTicketByUserAndTournament(Auth::user()->id, $tournament);

        if ($ticket) {
            return $this->response->success($ticket->toArray());
        }

        return $this->response->failed("No ticket found", 404);
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
    public function store(Request $request)
    {
        if( ! $tournaments = $request->get('tournaments') ) {
            return $this->response->failed('No tournament specified', 400);
        }

        try {
            $tickets = $this->tournamentService->storeTournamentTickets(Auth::user(), $tournaments);
        } catch (TournamentBuyInException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (TournamentEntryException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (BetLimitExceededException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed($e->getMessage(), 404);
        } catch (\Exception $e) {
            \Log::error("TicketsController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown Error");
        }

        return $this->response->success((new EloquentResourceCollection($tickets, 'TopBetta\Resources\Tournaments\TicketResource'))->toArray());
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

    public function rebuy(Request $request)
    {
        if (!$ticketId = $request->get('ticket_id')) {
            return $this->response->failed("No ticket specified", 400);
        }

        try{
            if( ! $this->buyInService->ticketBelongsToUser($ticketId, Auth::user()->id) ) {
                return $this->response->failed("Tournament ticket does not belong to current user", 401);
            }

            $ticket = $this->buyInService->rebuyIntoTournament($ticketId);

        } catch (TournamentBuyInException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error("TicketsController(rebuy): " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error occurred");
        }

        //unload tournament
        $ticket->tournament = null;

        return $this->response->success((new TicketResource($ticket))->toArray());
    }

    public function topup(Request $request)
    {
        if (!$ticketId = $request->get('ticket_id')) {
            return $this->response->failed("No ticket specified", 400);
        }

        try{
            if( ! $this->buyInService->ticketBelongsToUser($ticketId, Auth::user()->id) ) {
                return $this->response->failed("Tournament ticket does not belong to current user", 401);
            }

            $ticket = $this->buyInService->topupTournament($ticketId);
            $ticket->tournament = null;
        } catch (TournamentBuyInException $e) {
            return $this->response->failed($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error("TicketsController(topup): " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error occurred");
        }

        //unload tournament
        $ticket->tournament = null;

        return $this->response->success((new TicketResource($ticket))->toArray());
    }
}
