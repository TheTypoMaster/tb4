<?php namespace TopBetta\Http\Controllers\Frontend;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\TournamentBetService;

class UserTournamentBetsController extends Controller {


    /**
     * @var TournamentBetService
     */
    private $tournamentBetService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(TournamentBetService $tournamentBetService, ApiResponse $response)
    {
        $this->tournamentBetService = $tournamentBetService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @param $id
     * @param $tournamentId
     * @return \Illuminate\Http\JsonResponse
     */
	public function index($id, $tournamentId)
	{
        $bets = $this->tournamentBetService->getBetsForUserInTournamentWhereEventClosed($id, $tournamentId);

        return $this->response->success($bets->toArray());
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
