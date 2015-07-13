<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Response\ApiResponse;

class MeetingRaceSelectionsController extends Controller
{
    /**
     * @var MeetingService
     */
    private $meetingService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(MeetingService $meetingService, ApiResponse $response)
    {
        $this->meetingService = $meetingService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        try {
            $meeting = $this->meetingService->getMeetingWithSelections(
                $request->get('meeting_id'),
                $request->get('race_id', null)
            );
        } catch ( ModelNotFoundException $e ) {
            return $this->response->failed(array(), 404, "Meeting not found");
        }

        return $this->response->success($meeting['data']->toArray(), 200, array("selected_race" => $meeting['selected_race']));
    }

    public function getMeetingsWithSelectionsForMeeting(Request $request)
    {
        try {
            $meetings = $this->meetingService->getMeetingsWithSelectionForMeeting(
                $request->get('meeting_id'),
                $request->get('race_id', null)
            );
        } catch ( ModelNotFoundException $e) {
            return $this->response->failed(array(), 404, "Meeting not found");
        }

        return $this->response->success($meetings['data']->toArray(), 200, array("selected_race" => $meetings['selected_race']));
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
