<?php

namespace TopBetta\admin\controllers;

use TopBetta\Bet;
use User;
use Redirect;
use View;

class UserBetsController extends \BaseController
{

	/**
	 * @var Bet
	 */
	private $bet;
	protected $user;

	public function __construct(User $user, Bet $bet)
	{
		$this->user = $user;
		$this->bet = $bet;
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

		$bets = $this->bet
				->where('user_id', $user->id)
				->orderBy('created_date', 'desc')
				->paginate();

		return View::make('admin::bets.user.index', compact('user', 'bets'))
						->with('active', 'bets');
	}

}
