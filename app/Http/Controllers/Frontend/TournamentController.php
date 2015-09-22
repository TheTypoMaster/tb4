<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Resources\Tournaments\TournamentResourceService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\TournamentService;

class TournamentController extends Controller
{

    /**
     * @var TournamentService
     */
    private $tournamentService;
    /**
     * @var ApiResponse
     */
    private $response;
    /**
     * @var TournamentResourceService
     */
    private $resourceService;

    public function __construct(TournamentService $tournamentService, ApiResponse $response, TournamentResourceService $resourceService)
    {
        $this->tournamentService = $tournamentService;
        $this->response = $response;
        $this->resourceService = $resourceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $tournaments = $this->tournamentService->getVisibleTournaments(
                $request->get('type', 'racing'),
                $request->get('date', null)
            );
        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage(), 400);
        }

        return $this->response->success($tournaments->toArray());
    }

    public function getTournamentWithEvents(Request $request)
    {
        try {
            $tournament = $this->tournamentService->getTournamentWithEvents($request->get('tournament_id'), $request->get('event_id'));
        } catch (ModelNotFoundException $e) {
            return $this->response->failed("Tournament not found", 404);
        }
        return $this->response->success($tournament['data']->toArray(), 200, array_except($tournament, 'data'));
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
            $tournament = $this->resourceService->getTournament($id);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed("Tournament not found", 404);
        }

        return $this->response->success($tournament->toArray());

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
