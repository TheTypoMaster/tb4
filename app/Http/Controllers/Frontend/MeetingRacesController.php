<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Response\ApiResponse;

class MeetingRacesController extends Controller
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
     * @return ApiResponse
     */
    public function index(Request $request)
    {
        try {
            $meeting = $this->meetingService->getMeeting(
                $request->get('meeting_id'),
                true
            );
        } catch ( ModelNotFoundException $e ) {
            return $this->response->failed(array(), 404, "Meeting not found");
        }

        return $this->response->success(is_array($meeting) ? $meeting : $meeting->toArray());
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
