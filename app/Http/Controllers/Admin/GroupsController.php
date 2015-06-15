<?php

namespace TopBetta\Http\Controllers\Admin;

use View;
use Config;
use Input;
use Sentry;
use Redirect;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\AdminGroupsRepositoryInterface;

class GroupsController extends Controller {

    /**
     * @var AdminGroupsRepositoryInterface
     */
    private $groupsRepository;

    public function __construct(AdminGroupsRepositoryInterface $groupsRepository)
    {
        $this->groupsRepository = $groupsRepository;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$groups = $this->groupsRepository->findAll();

        return View::make('admin::groups.index', compact('groups'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$resources = Config::get('adminresources.resources');

        $otherPermissions = Config::get('adminresources.custom_permissions');

        return View::make('admin::groups.create', compact('resources', 'otherPermissions'));
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$group = Input::only(array('name', 'permissions'));

        $group = $this->groupsRepository->create($group);

        return Redirect::route('admin.groups.index')
            ->with(array('flash_message' => "Saved"));
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
        $group = Sentry::findGroupById($id);

        $resources = Config::get('adminresources.resources');

        $otherPermissions = Config::get('adminresources.custom_permissions');

        return View::make('admin::groups.edit', compact('group', 'resources', 'otherPermissions'));
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $group = Input::only(array('name', 'permissions'));

        $group = $this->groupsRepository->updateWithId($id, $group);

        return Redirect::route('admin.groups.index')
            ->with(array('flash_message' => "Saved"));
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
