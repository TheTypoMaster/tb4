<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 16/08/2014
 * File creation time: 11:58 PM
 * Project: tb4
 */

use Carbon\Carbon;
use TopBetta\Models\TournamentModel;
use TopBetta\Repositories\Contracts\EventStatusRepositoryInterface;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
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
            ->from('tbdb_tournament as tournament')
            ->leftJoin('tbdb_tournament as parent', 'parent.id', '=', 'tournament.parent_tournament_id')
            ->leftJoin('tbdb_event_group', 'tbdb_event_group.id', '=', 'tournament.event_group_id')
            ->leftJoin('tbdb_tournament_sport', 'tbdb_tournament_sport.id', '=', 'tbdb_event_group.sport_id')
            ->orderBy($this->order[0], $this->order[1])
            ->where('tournament.name', 'LIKE', "%$search%")
            ->orWhere('tournament.id', 'LIKE', "%$search%")
            ->with('parentTournament', 'eventGroup', 'sport')
            ->select(array('tournament.*'))
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
            ->from('tbdb_tournament as tournament')
            ->leftJoin('tbdb_tournament as parent', 'parent.id', '=', 'tournament.parent_tournament_id')
            ->leftJoin('tbdb_event_group', 'tbdb_event_group.id', '=', 'tournament.event_group_id')
            ->leftJoin('tbdb_tournament_sport', 'tbdb_tournament_sport.id', '=', 'tbdb_event_group.sport_id')
            ->orderBy($this->order[0], $this->order[1])
            ->where('tournament.start_date', '>=', $from)
            ->where('tournament.start_date', '<=', $to);

        if( $paged ) {
            return $model->select(array('tournament.*'))->paginate($paged);
        }

        return $model->get(array('tournament.*'));
    }

    public function findAllPaginated($relations = array(), $paginate = 15)
    {
        return $this->model
            ->from('tbdb_tournament as tournament')
            ->leftJoin('tbdb_tournament as parent', 'parent.id', '=', 'tournament.parent_tournament_id')
            ->leftJoin('tbdb_event_group as event_group', 'event_group.id', '=', 'tournament.event_group_id')
            ->leftJoin('tbdb_tournament_sport as sport', 'sport.id', '=', 'event_group.sport_id')
            ->orderBy($this->order[0], $this->order[1])
            ->select(array('tournament.*'))
            ->paginate();
    }

    public function getFinishedUnresultedTournaments()
    {
        return $this->model
            ->join('tb_tournament_event_group as eg', 'eg.id', '=', 'tbdb_tournament.event_group_id')
            ->join('tb_tournament_event_group_event as ege', 'ege.event_group_id', '=', 'eg.id')
            ->leftJoin('tbdb_event as e', function($q) {
                $q->on('e.id', '=', 'ege.event_id')
                    ->on('e.paid_flag', '=', \DB::raw(0));
            })
            ->where('tbdb_tournament.cancelled_flag', false)
            ->where('tbdb_tournament.paid_flag', false)
            ->groupBy('tbdb_tournament.id')
            ->havingRaw('COUNT(e.id) = 0')
            ->get(array('tbdb_tournament.*'));
    }

    public function getUnresultedTournamentsByCompetition($competition)
    {
        return $this->model
            ->where('paid_flag', false)
            ->where('cancelled_flag', false)
            ->where('event_group_id', $competition)
            ->get();
    }

    public function getVisibleSportTournaments(Carbon $date = null)
    {
        $model = $this->getVisibleTournamentBuilder($date);

        //join competition and sport and look for non racing sports
        $model->join('tb_tournament_event_group as eg', 'eg.id', '=', 't.event_group_id')
            ->join('tb_sports as s', 's.id', '=', 'eg.sport_id')
            ->whereNotIn('s.name', array(SportRepositoryInterface::SPORT_GALLOPING, SportRepositoryInterface::SPORT_GREYHOUNDS, SportRepositoryInterface::SPORT_HARNESS));

        return $model->get(array('t.*'));
    }

    public function getVisibleRacingTournaments(Carbon $date = null)
    {
        $model = $this->getVisibleTournamentBuilder($date);

        //join competition and sport and look for racing
        $model->join('tb_tournament_event_group as eg', 'eg.id', '=', 't.event_group_id')
            ->leftJoin('tb_sports as s', 's.id', '=', 'eg.sport_id')
            ->where(function($q) {
                $q->whereIn('s.name', array(SportRepositoryInterface::SPORT_GALLOPING, SportRepositoryInterface::SPORT_GREYHOUNDS, SportRepositoryInterface::SPORT_HARNESS))
                    ->orWhere('eg.sport_id', 0);
            });

        return $model->get(array('t.*'));
    }

    /**
     * @param Carbon $date
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getVisibleTournamentBuilder(Carbon $date = null)
    {
        $model = $this->model
            ->from('tbdb_tournament as t')
            ->where('t.status_flag', true)
            ->with('tickets')
            ->groupBy('t.id');

        if( $date ) {
            $model->where('t.start_date', '>=', $date->startOfDay()->toDateTimeString())->where('t.start_date', '<=', $date->endOfDay()->toDateTimeString());
        } else {
            $model->where('t.start_date', '>=', Carbon::now()->startOfDay());
        }

        return $model;
    }

    /**
     * get tournaments that start from today
     * used for drop down list in template
     * @return mixed
     */
    public function getTournamentList() {
        $tournament_list = $this->model->fromToday()->get();
        return $tournament_list;
    }

} 