<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Racing\RaceService;
use TopBetta\Services\Response\ApiResponse;

class RaceSelectionsController extends Controller
{
    /**
     * @var RaceService
     */
    private $raceService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(RaceService $raceService , ApiResponse $response)
    {
        $this->raceService = $raceService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id)
    {
        try {
            $model = $this->raceService->getRaceWithSelections($id);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed(array(), 404, "Race not found");
        }

        return $this->response->success(
            $this->raceService->formatForResponse($model)
        );
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
