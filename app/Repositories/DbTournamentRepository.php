<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Models\TournamentModel;
use TopBetta\Repositories\Contracts\TournamentRepositoryInterface;
use TopBetta\Services\Validation\TournamentValidator;


class DbTournamentRepository extends BaseEloquentRepository implements TournamentRepositoryInterface
{

    protected $order = array("start_date", "DESC");

    protected $model;

    public function __construct(TournamentModel $tournaments, TournamentValidator $tournamentValidator){
        $this->model = $tournaments;
        $this->validator = $tournamentValidator;
    }

    public function updateTournamentByEventGroupId($eventGroupId, $closeDate){
        return $this->model->where('event_group_id', $eventGroupId)
                    ->update(array('betting_closed_date' => $closeDate, 'end_date' => $closeDate));
    }


	public function getTournamentWithEventGroup($eventGroupId){
		$tournaments = $this->model->where('event_group_id', $eventGroupId)->get();
		if(!$tournaments) return null;
		return $tournaments->toArray();
	}

	public function getTournamentById($tournamentId) {
		$tournament = $this->model->where('id', $tournamentId)->get();
		if(!$tournament) return null;
		return $tournament->toArray();
	}

    public function search($search)
    {
        return $this->model
            ->orderBy('start_date', 'desc')
            ->where('name', 'LIKE', "%$search%")
            ->orWhere('id', 'LIKE', "%$search%")
            ->with('parentTournament', 'eventGroup', 'sport')
            ->paginate();
    }

    public function tournamentOfTheDay($todVenue, $day = null)
    {
        if( ! $day ) {
            $day = Carbon::now()->toDateString();
        }

        $nextDay = Carbon::createFromFormat('Y-m-d', $day)->addDay()->toDateString();

        return $this->model->where('tod_flag', $todVenue)->where('start_date', '>=', $day)->where('start_date', '<', $nextDay)->first();
    }

    public function findCurrentJackpotTournamentsByType($type, $excludedTournaments = null)
    {
        $model = $this->model->where('end_date', '>=', Carbon::now()->toDateTimeString())
            ->where('jackpot_flag', true);


        if($type == 'racing') {
            $model->whereIn('tournament_sport_id', array(1,2,3));
        } else if ($type == 'sport') {
            $model->whereNotIn('tournament_sport_id', array(1,2,3));
        }

        if($excludedTournaments) {
            $model->where('id', '!=', $excludedTournaments);
        }

        return $model->get();
    }

    public function getTournamentsInDateRange($from, $to, $paged = null)
    {
        $model = $this->model
            ->where('start_date', '>=', $from)
            ->where('start_date', '<=', $to);

        if( $paged ) {
            return $model->paginate($paged);
        }

        return $model->get();
    }

} 