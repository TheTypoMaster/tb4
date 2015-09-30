<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 22/07/2015
 * Time: 9:44 AM
 */

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Services\Tournaments\TournamentGroupService;

class TournamentGroupController extends Controller {

    /**
     * @var TournamentGroupService
     */
    private $tournamentGroupService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(TournamentGroupService $tournamentGroupService, ApiResponse $response)
    {
        $this->tournamentGroupService = $tournamentGroupService;
        $this->response = $response;
    }

    public function getVisibleTournamentGroupsWithTournaments(Request $request)
    {
        try{
            $groups = $this->tournamentGroupService->getGroupsWithTournaments(
                $request->get('type', ''),
                $request->get('date', null)
            );

        } catch (\InvalidArgumentException $e) {
            return $this->response->failed($e->getMessage(), 400);
        }

        return array('data' => $groups->toArray(), 'selected_event' => '');
    }
}