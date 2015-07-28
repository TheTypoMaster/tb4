<?php namespace TopBetta\Http\Controllers\Admin;

use Input;
use TopBetta\Http\Controllers\Controller;
use Redirect;
use TopBetta\Models\BetModel;
use TopBetta\Facades\BetLimitRepo;
use TopBetta\Repositories\Contracts\UserRepositoryInterface;
use TopBetta\Repositories\Contracts\UserTopBettaRepositoryInterface;
use TopBetta\Repositories\UserRepo;
use TopBetta\Models\UserModel;
use Request;
use TopBetta\Services\Email\Exceptions\EmailRequestException;
use TopBetta\Services\Email\ThirdPartyEmailServiceInterface;
use TopBetta\Services\Validation\Exceptions\ValidationException;
use View;

class UsersController extends Controller
{

	/**
	 * @var UserRepositoryInterface
	 */
	private $userRepo;
	protected $user;
	private $bet;
    /**
     * @var UserTopBettaRepositoryInterface
     */
    private $topbettaUserRepository;
    /**
     * @var ThirdPartyEmailServiceInterface
     */
    private $emailService;

    public function __construct(UserModel $user, UserRepositoryInterface $userRepo, BetModel $bet, UserTopBettaRepositoryInterface $topbettaUserRepository, ThirdPartyEmailServiceInterface $emailService)
	{
		$this->user = $user;
		$this->bet = $bet;
		$this->userRepo = $userRepo;
        $this->topbettaUserRepository = $topbettaUserRepository;
        $this->emailService = $emailService;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Request::get('q','');
		if ($search) {
			$users = $this->userRepo->search($search);
		} else {
			$users = $this->userRepo->findAllPaginated(array('topbettaUser'));
		}

		return View::make('admin.users.index')
						->with(compact('users','search'));
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
		$user = $this->user->find($id);

		if (is_null($user)) {
			// TODO: flash message user not found
			return Redirect::route('admin.users.index');
		}

		return View::make('admin.users.edit', compact('user'))
						->with('active', 'profile');
	}

    /**
     * Update the specified resource in storage.
     *
     * @param $userId
     * @return Response
     */
	public function update($userId)
	{
        $user = $this->userRepo->find($userId);

        $oldEmail = $user->email;

        if( Input::get('username') != $user->username && $this->userRepo->getUserByUsername(Input::get('username')) ) {
            return Redirect::route('admin.users.edit', array($userId))
                ->with('flash_message', 'username already exists');
        }

        if( Input::get('email') != $user->email && $this->userRepo->getUserByEmail(Input::get('email')) ) {
            return Redirect::route('admin.users.edit', array($userId))
                ->with('flash_message', 'email already exists');
        }

        $this->userRepo->updateWithId($userId, array(
            "name"     => Input::get('name'),
            "username" => Input::get('username'),
            "email"    => Input::get('email')
        ));

        $this->topbettaUserRepository->updateWithId($user->topbettauser->id, array(
            "first_name" => Input::get('first-name'),
            "last_name"  => Input::get('last-name'),
            "msisdn"     => Input::get('mobile'),
        ));

        try {
            $this->emailService->updateContact($oldEmail, $user->fresh());
        } catch ( EmailRequestException $e ) {
            return Redirect::route('admin.users.edit', array($userId))
                ->with(array('flash_message' => "Error updating Vision6 record"));
        }


		return Redirect::route('admin.users.edit', array($userId))
						->with('flash_message', 'Saved');
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
