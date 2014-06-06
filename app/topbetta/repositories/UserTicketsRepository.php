<?php
/**
 * Created by PhpStorm.
 * User: jason
 * Date: 6/06/14
 * Time: 1:36 PM
 */

namespace TopBetta\Repositories;


use Carbon\Carbon;
use TopBetta\TournamentTicket;
use User;
use Whoops\Example\Exception;

class UserTicketsRepository {

	/**
	 * @var \User
	 */
	private $user;

	/**
	 * @var \TopBetta\TournamentTicket
	 */
	private $ticket;

	function __construct(User $user, TournamentTicket $ticket) {
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
	 * @return \TopBetta\TournamentTicket
	 */
	public function getTicket()
	{
		return $this->ticket;
	}

	/**
	 * @param \TopBetta\TournamentTicket $ticket
	 */
	public function setTicket($ticket)
	{
		$this->ticket = $ticket;
	}
}