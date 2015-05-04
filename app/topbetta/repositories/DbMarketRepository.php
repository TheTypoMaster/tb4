<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 28/04/2014
 * File creation time: 5:32 PM
 * Project: tb4
 */

use TopBetta\RaceMarket;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\SportsMarket;
use TopBetta\SportsSelection;
use TopBetta\SportsSelectionResults;

use TopBetta\Repositories\BetResultRepo;


class DbMarketsRepository extends BaseEloquentRepository implements MarketRepositoryInterface {

    protected $model;

	protected $betresults;

    public function __construct(RaceMarket $model,
								BetResultRepo $betresults){
        $this->model = $model;
		$this->betresults = $betresults;
    }

    /**
     * Market with type name and event name used for filtered list
     * @param $search
     * @return mixed
     */
    public function search($search)
    {
        return $this->model->join('tbdb_market_type', 'tbdb_market.market_type_id', '=', 'tbdb_market_type.id')
                    ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
                    ->where('tbdb_market_type.name', 'LIKE', "%$search%")
                    ->orWhere('tbdb_event.name', 'LIKE', "%$search%")
                    ->select('tbdb_market.id as id', 'tbdb_market.display_flag as display_flag',
                        'tbdb_market.created_at as created_at', 'tbdb_market.updated_at as updated_at',
                        'tbdb_market_type.name as market_type_name', 'tbdb_event.name as event_name',
                        'tbdb_market.market_status as market_status')
                    ->orderBy('tbdb_market.id', 'DESC')
                    ->paginate();
    }

    /**
     * All Markets with type name and event name
     * @return mixed
     */
    public function allMarkets()
    {
        return $this->model->join('tbdb_market_type', 'tbdb_market.market_type_id', '=', 'tbdb_market_type.id')
                    ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
                    ->select('tbdb_market.id as id', 'tbdb_market.display_flag as display_flag',
                        'tbdb_market.created_at as created_at', 'tbdb_market.updated_at as updated_at',
                        'tbdb_market_type.name as market_type_name', 'tbdb_event.name as event_name',
                        'tbdb_market.market_status as market_status')
                    ->orderBy('tbdb_market.id', 'DESC')
                    ->paginate();
    }

    /**
     * Single Market with type name and event name used edit
     * @param $id
     * @return mixed
     */
    public function findWithMarketTypePlusEvent($id)
    {
        return $this->model->join('tbdb_market_type', 'tbdb_market.market_type_id', '=', 'tbdb_market_type.id')
                    ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
                    ->where('tbdb_market.id', $id)
                    ->select('tbdb_market.id as id', 'tbdb_market.display_flag as display_flag',
                        'tbdb_market.created_at as created_at', 'tbdb_market.updated_at as updated_at',
                        'tbdb_market_type.name as market_type_name', 'tbdb_event.name as event_name',
                        'tbdb_market.market_status as market_status')
                   ->first();
    }

    public function getMarketEventStartTime($marketId){
        return RaceMarket::find($marketId)->event()->pluck('start_date');
    }

    /**
     * Save results for a sports market and result bets for this market
     *
     * @param $marketId
     * @param $status
     * @param $score
     * @return array
     */
    public function resultMarket($marketId, $status, $score)
    {
        $errors = [];
        // update market status
        $sportsMarket = SportsMarket::find($marketId);
        if (!$sportsMarket) {
            $errors[] = "Can't find market with id $marketId";
            return $errors;
        } else {
            $sportsMarket->update(['market_status' => $status]);
        }

        if ($status == 'C' || $status == 'R') {
            $selectionId = SportsSelection::where('market_id', $marketId)
                ->where('name', $score)
                ->pluck('id');

            if (!$selectionId) {
                $errors[] = "Can't find selection/score: $score, for this market";
                return $errors;
            }

            // TODO: what should we do if we had a previous result? How do we find it?
            $selectionResultModel = SportsSelectionResults::firstOrNew([
                'selection_id' => $selectionId,
                'position' => 1
            ]);

            \Log::info('Resulting sport market id: ' . $marketId . ' win selection id: ' . $selectionId . ' score: ' . $score);

            if ($selectionResultModel->save()) {
                // result all bets for this market
                $extMarket = SportsMarket::find($marketId);
                if ($extMarket) {
                    \Log::info('Resulting bets for ext market id: ' . $extMarket->external_market_id);
                    //$betResultRepo = new BetResultRepo();
					$this->betresults->resultAllSportBetsForMarket($extMarket->external_market_id);
                } else {
                    \Log::info('Couldnt find market id: ' . $marketId);
                }
            } else {
                $errors[] = "Problem saving results for selection: $selectionId";
            }
        } else {
            $errors[] = "Wrong status provided: $status";
        }

        return $errors;
    }

    public function getMarketsForEventId($id){

        $markets = $this->model->join('tbdb_market_type', 'tbdb_market_type.id', '=', 'tbdb_market.market_type_id')
                                ->where('tbdb_market.event_id', $id)
                                ->where('tbdb_market.market_status', 'O')
                                ->select(array('tbdb_market.id as market_id', 'tbdb_market_type.name as market_name', 'tbdb_market.line as market_handicap'))
                                ->get();

        if(!$markets) return null;

        return $markets->toArray();
    }

    public function getAllMarketsForEvent($eventId)
    {
        return $this->model->join('tbdb_market_type', 'tbdb_market.market_type_id', '=', 'tbdb_market_type.id')
            ->join('tbdb_event', 'tbdb_market.event_id', '=', 'tbdb_event.id')
            ->where('tbdb_market.event_id', $eventId)
            ->select('tbdb_market.id as id', 'tbdb_market.display_flag as display_flag',
                'tbdb_market.created_at as created_at', 'tbdb_market.updated_at as updated_at',
                'tbdb_market_type.name as market_type_name', 'tbdb_event.name as event_name',
                'tbdb_market.market_status as market_status')
            ->orderBy('tbdb_market.id', 'DESC')
            ->paginate();
    }
}