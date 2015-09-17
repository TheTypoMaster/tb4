<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Resources\Cache\CachedRaceResourceService;
use TopBetta\Services\Resources\RaceResourceService;
use TopBetta\Services\Response\ApiResponse;

class RaceController extends Controller
{

    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(ApiResponse $response)
    {
        $this->response = $response;
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
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
    public function show($id, CachedRaceResourceService $raceResourceService)
    {
        try {
            $race = $raceResourceService->getRace($id);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed("Race not found", 404);
        } catch (\Exception $e) {
            \Log::error('RaceContrller: ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown Error");
        }

        return $this->response->success($race->toArray());
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
