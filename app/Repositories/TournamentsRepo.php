<?php

namespace TopBetta\Repositories;

use TopBetta\Tournament;

/**
 * Tournament Repo for admin interface
 *
 * @author mic
 */
class TournamentsRepo
{

	/**
	 * @var Tournament
	 */
	private $tournament;

	public function __construct(Tournament $tournament)
	{
		$this->tournament = $tournament;
	}

	public function search($search)
	{
		return $this->tournament
						->orderBy('start_date', 'desc')
						->where('name', 'LIKE', "%$search%")
						->orWhere('id', 'LIKE', "%$search%")
						->with('parentTournament', 'eventGroup', 'sport')
						->paginate();
	}

	public function allTournaments()
	{
		return $this->tournament
						->orderBy('start_date', 'desc')
						->with('parentTournament', 'eventGroup', 'sport')
						->paginate();
	}

}
