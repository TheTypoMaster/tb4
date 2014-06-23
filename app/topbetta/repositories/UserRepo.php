<?php

namespace TopBetta\Repositories;

use TopBetta\Tournament;
use TopBetta\TournamentTicket;
use User;

/**
 * Description of UserRepo
 *
 * @author mic
 */
class UserRepo
{

	/**
	 * @var Tournament
	 */
	private $tournament;

	/**
	 * @var TournamentTicket
	 */
	private $tournamentTicket;

	/**
	 * @var User
	 */
	private $user;

	public function __construct(User $user, Tournament $tournament, TournamentTicket $tournamentTicket)
	{
		$this->user = $user;
		$this->tournamentTicket = $tournamentTicket;
		$this->tournament = $tournament;
	}

	public function search($search)
	{
		return $this->user
						->where('username', 'LIKE', "%$search%")
						->orWhere('name', 'LIKE', "%$search%")
						->orWhere('id', 'LIKE', "%$search%")
						->orWhere('email', 'LIKE', "%$search%")
						->with('topbettaUser')
						->paginate();
	}

	public function allUsers()
	{
		return $this->user
						->with('topbettaUser')
						->paginate();
	}

	/**
	 * Get all the tournaments for this user
	 * based on tournament tickets they have
	 * 
	 * @param type $userId
	 * @return type
	 */
	public function tournaments($userId)
	{
		$tickets = $this->user
				->find($userId)
				->tournamentTickets
				->lists('tournament_id');

		if (!$tickets) {
			return array();
		}

		return $this->tournament
						->whereIn('id', $tickets)
						->paginate();
	}

}
