<?php

namespace TopBetta\Http\Controllers\Admin;

use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Tournaments\TournamentEventGroupService;
use Input;

class TournamentEventGroupController extends Controller
{

    public function __construct(TournamentEventGroupService $tournamentEventGroupService) {

        $this->tournamentEventGroupService = $tournamentEventGroupService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $event_groups = $this->tournamentEventGroupService->getAllEventGroups();
        return view('admin.tournaments.event-groups.index')->with(['event_groups' => $event_groups]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $event_group_list = $this->tournamentEventGroupService->getAllEventGroupsToArray();
        return view('admin.tournaments.event-groups.create')->with(['event_group_list' => $event_group_list]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $tournamentEventGroupModel = TournamentEventGroupModel::create(['name' => Input::get('event_group_name')]);


        return 'create new event group';
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
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
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
