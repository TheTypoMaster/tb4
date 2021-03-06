<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 21/11/14
 * File creation time: 08:47
 * Project: tb4
 */

use TopBetta\Models\SelectionModel;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;
use TopBetta\Repositories\Traits\SportsResourceRepositoryTrait;

class DbSelectionRepository extends BaseEloquentRepository implements SelectionRepositoryInterface
{
    use SportsResourceRepositoryTrait;

    protected $selections;

    function __construct(SelectionModel $selections)
    {
        $this->model = $selections;
    }

    /**
     * Selection with type name and event name used for filtered list
     * @param $search
     * @return mixed
     */
    public function search($search, $market = null)
    {
        $model = $this->model->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')
            ->join('tbdb_selection_price', 'tbdb_selection_price.selection_id', '=', 'tbdb_selection.id')
            ->leftjoin('tbdb_selection_result', 'tbdb_selection_result.selection_id', '=', 'tbdb_selection.id')
            ->where(function ($q) use ($search) {
                $q->where('tbdb_selection.name', 'LIKE', "%$search%")
                    ->orWhere('tbdb_event.name', 'LIKE', "%$search%")
                    ->orWhere('tbdb_event_group.name', 'LIKE', "%$search%");
            });

        if ($market) {
            $model->where('market_id', $market);
        }

        return $model->select('tbdb_selection.*', 'tbdb_selection_status.name as status_name', 'tbdb_event.id as event_id',
            'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name', 'tbdb_selection_price.override_odds as override_odds', 'tbdb_selection_price.override_type as override_type',
            'tbdb_selection_price.win_odds as win_odds', 'tbdb_selection_price.place_odds as place_odds', 'tbdb_selection_price.id as selection_price_id', 'tbdb_selection_price.line as line')
            ->paginate();
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function allSelections($limit = 10)
    {

        // $results =  new \stdClass();
        //$results->page = $page;
        // $results->limit = $limit;
        // $results->totalItems = 0;
        // $results->items = array();

        $selections = $this->model->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->leftjoin('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->leftjoin('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->leftjoin('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->leftjoin('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')
            ->leftjoin('tbdb_selection_price', 'tbdb_selection_price.selection_id', '=', 'tbdb_selection.id')
            ->leftjoin('tbdb_selection_result', 'tbdb_selection_result.selection_id', '=', 'tbdb_selection.id')
            ->select('tbdb_selection.*', 'tbdb_selection_status.name as status_name',
                'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name', 'tbdb_selection_price.override_odds as override_odds', 'tbdb_selection_price.override_type as override_type',
                'tbdb_selection_price.win_odds as win_odds', 'tbdb_selection_price.place_odds as place_odds', 'tbdb_selection_price.id as selection_price_id', 'tbdb_selection_price.line as line')
            ->paginate($limit);

        // $results->totalItems = $this->model->count();
        // $results->items = $selections->all();

        return $selections;
    }

    /**
     * Single Selections with type name and event name used edit
     * @param $id
     * @return mixed
     */
    public function findWithMarketTypePlusEvent($id)
    {
        return $this->model->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')
            ->where('tbdb_selection.id', $id)
            ->select('tbdb_selection.*', 'tbdb_selection_status.name as status_name',
                'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name')
            ->first();
    }

    /**
     * Check if a selection exists.
     *
     * @param $meetingId
     * @param $raceNo
     * @param $runnerNo
     * @return mixed
     */
    public function getSelectionIdFromMeetingIdRaceNumberSelectionName($meetingId, $raceNo, $runnerNo)
    {
        return $this->model->join('tbdb_market', 'tbdb_selection.market_id', '=', 'tbdb_market.id')
            ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
            ->join('tbdb_event_group_event', 'tbdb_event.id', '=', 'tbdb_event_group_event.event_id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->where('tbdb_event_group.external_event_group_id', $meetingId)
            ->where('tbdb_event.number', $raceNo)
            ->where('tbdb_selection.number', $runnerNo)
            ->select('tbdb_selection.*')
            ->first();
    }

    public function getSelectionsforMarketId($id)
    {
        $selections = $this->model->join('tbdb_selection_price', 'tbdb_selection_price.selection_id', '=', 'tbdb_selection.id')
            ->where('tbdb_selection.market_id', $id)
            ->where('tbdb_selection_price.win_odds', '>', '1')
            ->where('tbdb_selection.selection_status_id', '=', '1')
            ->select('tbdb_selection.name as selection_name', 'tbdb_selection_price.win_odds as selection_odds', 'tbdb_selection_price.line as selection_handicap')
            ->get();
        if (!$selections) return null;

        return $selections->toArray();
    }

    public function updateWithId($id, $data)
    {
        parent::updateWithId($id, array_except($data, array('team', 'player')));

        $selection = $this->model->find($id);

        //assosciate teams
        if (is_array($team = array_get($data, 'team', false))) {
            $selection->team()->sync($team);
        }

        //assosciate players
        if (is_array($player = array_get($data, 'player', false))) {
            $selection->player()->sync($player);
        }

        return $selection->toArray();
    }

    public function getByExternalIds($externalSelectionId, $externalMarketId, $externalEventId)
    {
        $selection = $this->model
            ->where('external_selection_id', $externalSelectionId)
            ->where('external_market_id', $externalMarketId)
            ->where('external_event_id', $externalEventId)
            ->first();

        if ($selection) {
            return $selection->toArray();
        }

        return null;
    }

    public function getModelByExternalIds($externalSelectionId, $externalMarketId, $externalEventId)
    {
        return $this->model
            ->where('external_selection_id', $externalSelectionId)
            ->where('external_market_id', $externalMarketId)
            ->where('external_event_id', $externalEventId)
            ->first();
    }

    public function getByExternalIdsAndName($externalMarketId, $externalEventId, $name)
    {
        $selection = $this->model
            ->where('external_market_id', $externalMarketId)
            ->where('external_event_id', $externalEventId)
            ->where('name', $name)
            ->first();

        if ($selection) {
            return $selection->toArray();
        }

        return null;
    }


    public function getSeletcionIdByExternalId($externalId)
    {
        $selection = $this->model->where('external_selection_id', $externalId)
            ->value('id');
        if (!$selection) return null;
        return $selection;
    }

    public function getSelectionByExternalId($externalId)
    {
        return $this->model->where('external_selection_id', $externalId)
            ->first();
    }

    public function getAllSelectionsForMarket($marketId)
    {
        return $this->model->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->join('tbdb_selection_status', 'tbdb_selection_status.id', '=', 'tbdb_selection.selection_status_id')
            ->join('tbdb_selection_price', 'tbdb_selection_price.selection_id', '=', 'tbdb_selection.id')
            ->leftjoin('tbdb_selection_result', 'tbdb_selection_result.selection_id', '=', 'tbdb_selection.id')
            ->where('tbdb_selection.market_id', $marketId)
            ->select('tbdb_selection.*', 'tbdb_selection_status.name as status_name',
                'tbdb_event_group.name as competition_name', 'tbdb_event.name as event_name', 'tbdb_selection_price.override_odds as override_odds', 'tbdb_selection_price.override_type as override_type',
                'tbdb_selection_price.win_odds as win_odds', 'tbdb_selection_price.place_odds as place_odds', 'tbdb_selection_price.id as selection_price_id', 'tbdb_selection_price.line as line')
            ->paginate();
    }

    public function getSelectionsForEvent($event)
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->where('e.id', $event)
            ->groupBy('s.id');

        return $this->model->hydrate($builder->get(array('s.*')))
            ->load(array(
                'price',
                'result',
                'team',
                'player'
            ));
    }

    public function getSelectionsForMarkets($markets)
    {
        $builder = $this->getVisibleSportsEventBuilder()
            ->whereIn('m.id', $markets)
            ->groupBy('s.id');

        return $this->model->hydrate($builder->get(array('s.*')))
            ->load(array(
                'price',
                'result',
                'team',
                'player'
            ));
    }

    public function getSelectionsForRace($race)
    {
        return $this->model
            ->join('tbdb_market', 'tbdb_market.id', '=', 'tbdb_selection.market_id')
            ->join('tbdb_event', 'tbdb_event.id', '=', 'tbdb_market.event_id')
            ->join('tbdb_event_group_event', 'tbdb_event_group_event.event_id', '=', 'tbdb_event.id')
            ->join('tbdb_event_group', 'tbdb_event_group.id', '=', 'tbdb_event_group_event.event_group_id')
            ->where('tbdb_event.id', $race)
            ->with(array(
                'result',
                'price',
                'runner',
                'runner.owner',
                'runner.trainer',
                'form',
                'lastStarts'
            ))
            ->get(array('tbdb_selection.*', 'tbdb_event_group.type_code as type_code'));
    }

    public function getSelectionsByMarket($market)
    {
        return $this->model
            ->where('market_id', $market)
            ->get();
    }

    public function getByExternalId($id)
    {
        return $this->model->where('external_selection_id', $id)->first();
    }

    public function getBySerenaId($id)
    {
        return $this->model->where('serena_selection_id', $id)->first();
    }
}