<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Resources\Tournaments\LeaderboardResourceService;
use TopBetta\Services\Response\ApiResponse;

class TournamentLeaderboardController extends Controller
{
    const LOG_PREFIX = 'TournamentLeaderboardController';

    /**
     * @var LeaderboardResourceService
     */
    private $leaderboardResourceService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(LeaderboardResourceService $leaderboardResourceService, ApiResponse $response)
    {
        $this->leaderboardResourceService = $leaderboardResourceService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id, Request $request)
    {
        try {
            $leaderboard = $this->leaderboardResourceService->getTournamentLeaderboard($id, $request->get('per_page', 50), $request->get('only_qualified', false));
        } catch (\Exception $e) {
            \Log::error(self::LOG_PREFIX . ':' . $e->getMessage() . 'PHP_EOL' . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success($leaderboard->toArray());
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
