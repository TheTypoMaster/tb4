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
use TopBetta\Services\Country\CountryService;
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

    public function __construct(UserModel $user, UserRepositoryInterface $userRepo, BetModel $bet, UserTopBettaRepositoryInterface $topbettaUserRepository, ThirdPartyEmailServiceInterface $emailService,
								CountryService $countryService)
	{
		$this->user = $user;
		$this->bet = $bet;
		$this->userRepo = $userRepo;
        $this->topbettaUserRepository = $topbettaUserRepository;
        $this->emailService = $emailService;
		$this->countryService = $countryService;
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
		$topbetta_user_record = $user->topbettauser()->first();
		$country_list = $this->countryService->getCountryList();

		if (is_null($user)) {
			// TODO: flash message user not found
			return Redirect::route('admin.users.index');
		}

		return View::make('admin.users.edit', compact('user', 'topbetta_user_record', 'country_list'))
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

        $data = array(  "name"     => Input::get('name'),
            "username" => Input::get('username'),
            "email"    => Input::get('email'));

        if(Input::get('password')) {
            $data['password'] = md5(Input::get('password'));
        }

        $this->userRepo->updateWithId($userId, $data);



        $this->topbettaUserRepository->updateWithId($user->topbettauser->id, array(
            "first_name" => Input::get('first-name'),
            "last_name"  => Input::get('last-name'),
            "msisdn"     => Input::get('mobile'),
			"identity_doc"   => Input::get('doc_type'),
		    "identity_doc_id" => Input::get('doc_id'),
		    "bsb_number"  => Input::get('bsb_number'),
			"bank_account_number" => Input::get('bank_account_number'),
			"account_name" => Input::get('bank_account_name'),
			"bank_name" => Input::get('Bank_name'),
			"source" => Input::get('source'),
			"self_exclusion_date" => Input::get('exclusion_date'),
			"street" => Input::get('street'),
			"city" => Input::get('suburb'),
			"state" => Input::get('state'),
			"country" => Input::get('country'),
			"postcode" => Input::get('postcode'),
			"heard_about" => Input::get('heard_about_us'),
			"bet_limit" => Input::get('bet_limit')
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
