<?php namespace TopBetta\Http\Controllers\Admin;

use View;
use Input;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Redirect;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Repositories\Contracts\AdminGroupsRepositoryInterface;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;

class UserPermissionsController extends Controller {

    /**
     * @var AdminGroupsRepositoryInterface
     */
    private $groupsRepository;
    /**
     * @var UserRepositoryInterface
     */
    private $userRepositoryInterface;

    public function __construct(AdminGroupsRepositoryInterface $groupsRepository, UserRepositoryInterface $userRepositoryInterface)
    {
        $this->groupsRepository = $groupsRepository;
        $this->userRepositoryInterface = $userRepositoryInterface;
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

	   $user = Sentry::findUserById($id);

        $groups = $this->groupsRepository->findAll();

        return View::make('admin.users.permissions.edit', compact('user', 'groups'))
            ->with('active', 'user-permissions');
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$permissions = Input::get("permissions", array());

        $groups = Input::get("groups", array());

		$status = Input::get('status');

        $user = Sentry::findUserById($id);

		$current_status = $user->block;

		if($status == '1') {
			if($current_status == '0') {
				$user->block = '1';
			} else if($current_status == '1') {
				$user->block = '0';
			}
		}

        $user->permissions = $permissions;

        $user->groups()->sync($groups);

        $user->save();

        return Redirect::route('admin.user-permissions.edit', array($user->id))
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
