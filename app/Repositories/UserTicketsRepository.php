<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 1:36 PM
 */

namespace TopBetta\Repositories;


use Carbon\Carbon;
use TopBetta\Models\TournamentTicket;
use TopBetta\Models\UserModel;
use Whoops\Example\Exception;

class UserTicketsRepository {

	/**
	 * @var \User
	 */
	private $user;

	/**
	 * @var \TopBetta\Models\TournamentTicket
	 */
	private $ticket;

	function __construct(UserModel $user, TournamentTicket $ticket) {
		$this->user = $user;
		$this->ticket = $ticket;
	}

	/**
	 * Gets all the tickets and tournaments for a user
	 */
	public function getUsersTicketsAndTournaments($endDate = null) {

		$user = $this->getUser();
		$tournamentTickets = $user->tournamentTickets()
			->join('tbdb_tournament', 'tbdb_tournament.id','=','tbdb_tournament_ticket.tournament_id');

		if ($endDate !== null) {
			$tournamentTickets
				->where('tbdb_tournament.end_date','>=', $endDate);
		}

		return $tournamentTickets->get();
	}

	/**
	 * @return \User
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @param \User $user
	 */
	public function setUser($user)
	{
		$this->user = $user;
	}

	/**
	 * @return \TopBetta\Models\TournamentTicket
	 */
	public function getTicket()
	{
		return $this->ticket;
	}

	/**
	 * @param \TopBetta\Models\TournamentTicket $ticket
	 */
	public function setTicket($ticket)
	{
		$this->ticket = $ticket;
	}
}
