<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Contracts\Validation\UnauthorizedException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Resources\EloquentResourceCollection;
use TopBetta\Services\Betting\Exceptions\BetPlacementException;
use TopBetta\Services\Betting\Exceptions\BetSelectionException;
use TopBetta\Services\Resources\Tournaments\TournamentBetResourceService;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\Betting\Exceptions\TournamentBetLimitExceededException;
use TopBetta\Services\Tournaments\TournamentBetService;
use TopBetta\Services\Validation\Exceptions\ValidationException;

class TournamentBetsController extends Controller
{
    /**
     * @var TournamentBetService
     */
    private $betService;
    /**
     * @var ApiResponse
     */
    private $response;
    /**
     * @var TournamentBetResourceService
     */
    private $betResourceService;

    public function __construct(TournamentBetService $betService, ApiResponse $response, TournamentBetResourceService $betResourceService)
    {
        $this->betService = $betService;
        $this->response = $response;
        $this->betResourceService = $betResourceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (! $tournament = $request->get('tournament_id') ) {
            return $this->response->failed("No tournament specified", 400);
        }

        try {
            $bets = $this->betResourceService->getBetsForUserInTournament(\Auth::user()->id, $tournament);
        } catch (\Exception $e) {
            \Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

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
    public function store(Request $request)
    {
        try {
            $bets = $this->betService->placeBet($request->all());
        } catch(ValidationException $e) {
            return $this->response->failed($e->getErrors(), 400);
        } catch (UnauthorizedException $e) {
            return $this->response->failed($e->getMessage(), 401);
        } catch ( BetPlacementException $e ) {
            return $this->response->failed($e->getMessage());
        } catch ( BetSelectionException $e ) {
            return $this->response->failed(array($e->getMessage(), "selection" => $e->getSelection() ? $e->getSelection()->name : null) );
        } catch ( TournamentBetLimitExceededException $e ) {
            return $this->response->failed($e->getMessage());
        } catch ( \Exception $e ) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success(
            is_array($bets) ? array_map(function ($v) {return $v->toArray();}, $bets) : ($bets ? $bets->toArray() : array())
        );
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
