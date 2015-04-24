<?php

namespace TopBetta\Frontend\Controllers;

use TopBetta\Repositories\BaseEloquentRepository;
use TopBetta\Services\Response\ApiResponse;

abstract class AbstractResourceController extends \BaseController {
    /**
     * @var ApiResponse
     */
    private $apiResponse;

    /**
     * Get the resource repository
     * @return BaseEloquentRepository;
     */
    abstract public function getResourceRepository();

    public function __construct(ApiResponse $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }


    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$resources = $this->getResourceRepository()->findAll();

        return $this->apiResponse->success($resources->toArray());
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
		$resource = $this->getResourceRepository()->find($id);

        if($resource) {
            return $this->apiResponse->success($resource->toArray());
        }

        return $this->apiResponse->failed(array("resource not found"));
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
