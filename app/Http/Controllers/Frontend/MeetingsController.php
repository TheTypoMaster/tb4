<?php

namespace TopBetta\Http\Controllers\Frontend;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Racing\MeetingService;
use TopBetta\Services\Response\ApiResponse;

class MeetingsController extends Controller
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
     * @return ApiResponse
     */
    public function index(Request $request)
    {
        $meetings = $this->meetingService->getMeetingsForDate(
            $request->get('date', null),
            $request->get('type', null)
        );

        return $this->response->success($meetings->toArray());
    }

    public function getMeetingsWithRaces(Request $request)
    {
        $meetings = $this->meetingService->getMeetingsForDate(
            $request->get('date', null),
            $request->get('type', null),
            true
        );

        return $this->response->success(is_array($meetings) ? $meetings : $meetings->toArray());
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
            $meeting = $this->meetingService->getMeeting($id);
        } catch ( ModelNotFoundException $e ) {
            return $this->response->failed(array(), 404, "Meeting not found");
        }

        return $this->response->success(
            $meeting->toArray()
        );
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
