<?php

namespace TopBetta\Repositories;

use TopBetta\BetSelection;
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
	 * @var BetSelection
	 */
	private $betSelection;

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

	public function __construct(User $user, Tournament $tournament, TournamentTicket $tournamentTicket, BetSelection $betSelection)
	{
		$this->user = $user;
		$this->tournamentTicket = $tournamentTicket;
		$this->tournament = $tournament;
		$this->betSelection = $betSelection;
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
                        ->orderBy('end_date', 'DESC')
						->paginate();
	}

	public function sumUserBetsForSelectionAndType($selectionId, $betType, $userId)
	{
		return $this->betSelection
						->join('tbdb_bet AS b', 'b.id', '=', 'tbdb_bet_selection.bet_id')
						->where('selection_id', $selectionId)
						->where('position', 0)
						->where('bet_type_id', $betType)
						->where('b.user_id', $userId)
						->sum(\DB::raw('b.bet_amount + b.bet_freebet_amount'));
	}

}
