<?php namespace TopBetta\Http\Controllers\Admin;

use TopBetta\Http\Controllers\Controller;
use TopBetta\Models\BetLimitUser;
use Redirect;
use TopBetta\Repositories\BetLimitRepo;
use Input;
use TopBetta\Models\UserModel;
use Validator;
use View;

class UserBetLimitsController extends Controller
{

	/**
	 * @var BetLimitUser
	 */
	private $betLimitUser;

	/**
	 * @var BetLimitRepo
	 */
	protected $betLimitRepo;
	protected $user;

	public function __construct(UserModel $user, BetLimitRepo $betLimitRepo, BetLimitUser $betLimitUser)
	{
		$this->user = $user;
		$this->betLimitRepo = $betLimitRepo;
		$this->betLimitUser = $betLimitUser;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index($userId)
	{
		$user = $this->user->find($userId);

		if (is_null($user)) {
			return Redirect::route('admin.users.index')
							->with('flash_message', 'User not found!');
		}

		$betLimits = $this->betLimitRepo->getBetLimitsForUser($user->id, TRUE);
		$betLimitTypes = $this->betLimitRepo->getAllLimitTypesNicknames();

		return View::make('admin.betlimits.user.index', compact('user', 'betLimits', 'betLimitTypes'))
						->with('active', 'bet-limits');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($userId)
	{
		$user = $this->user->find($userId);
		$betLimitTypes = $this->betLimitRepo->getAllLimitTypesNicknames();

		if (is_null($user)) {
			return Redirect::route('admin.users.index')
							->with('flash_message', 'User not found!');
		}

		return View::make('admin.betlimits.user.create', compact('user', 'betLimitTypes'))
						->with('active', 'bet-limits');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($userId)
	{
		$input = Input::all();
		
		// check if a bet limit already exists for this bet_limit_type_id
		if ($this->checkBetLimitAlreadyExistsForUser($input)) {
			return Redirect::route('admin.users.bet-limits.create', array($userId))
							->withInput()
							->withErrors(array('This bet limit already exists'))
							->with('flash_message', 'There were validation errors.');
		}

		$validation = Validator::make($input, BetLimitUser::$rules);

		if ($validation->passes()) {
			$this->betLimitUser->create($input);

			return Redirect::route('admin.users.bet-limits.index', $userId)
							->with('flash_message', 'Bet Limit Added.');
		}

		return Redirect::route('admin.users.bet-limits.create', array($userId))
						->withInput()
						->withErrors($validation)
						->with('flash_message', 'There were validation errors.');
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
	public function edit($userId, $betLimitId)
	{
		$user = $this->user->find($userId);
		$betLimit = $this->betLimitRepo->getUserBetLimitWithId($betLimitId);
		$betLimitTypes = $this->betLimitRepo->getAllLimitTypesNicknames();

		if (is_null($user)) {
			return Redirect::route('admin.users.index')
							->with('flash_message', 'User not found!');
		}

		return View::make('admin.betlimits.user.edit', compact('user', 'betLimit', 'betLimitTypes'))
						->with('active', 'bet-limits');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($userId, $betLimitId)
	{
		$input = array_except(Input::all(), '_method');
		
		$validation = Validator::make($input, BetLimitUser::$rules);

		if ($validation->passes()) {
			$betLimit = $this->betLimitUser->find($betLimitId);
			$betLimit->update($input);

			return Redirect::route('admin.users.bet-limits.index', $userId)
							->with('flash_message', 'Bet Limit Updated.');
		}

		return Redirect::route('admin.users.bet-limits.edit', array($userId, $betLimitId))
						->withInput()
						->withErrors($validation)
						->with('flash_message', 'There were validation errors.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($userId, $betLimitId)
	{
		$this->betLimitUser->find($betLimitId)->delete();

		return Redirect::route('admin.users.bet-limits.index', $userId)
						->with('flash_message', 'Bet Limit Removed.');
	}
	
	private function checkBetLimitAlreadyExistsForUser($input){
		return $this->betLimitUser->where('bet_limit_type_id', $input['bet_limit_type_id'])
				->where('user_id', $input['user_id'])
				->count();
	}

}
