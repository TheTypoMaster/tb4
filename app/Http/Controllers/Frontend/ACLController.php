<?php

namespace TopBetta\Http\Controllers\Frontend;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

use TopBetta\Http\Requests;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Resources\ACLResourceService;
use TopBetta\Services\Response\ApiResponse;

class ACLController extends Controller
{
    /**
     * @var ACLResourceService
     */
    private $ACLResourceService;
    /**
     * @var ApiResponse
     */
    private $response;

    public function __construct(ACLResourceService $ACLResourceService, ApiResponse $response)
    {
        $this->ACLResourceService = $ACLResourceService;
        $this->response = $response;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

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
     * @param $affiliate
     * @param $acl
     * @return Response
     */
    public function show($affiliate, $acl)
    {
        try {
            $acl = $this->ACLResourceService->getACLByAffiliateAndACLCode($affiliate, $acl);
        } catch (ModelNotFoundException $e) {
            return $this->response->failed("ACL not found", 404);
        } catch (\Exception $e) {
            \Log::error("ACLController: " . $e->getMessage() . PHP_EOL . $e->getTraceAsString());
            return $this->response->failed("Unknown error");
        }

        return $this->response->success($acl->toArray(), 200, $acl->getMetaData());
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
