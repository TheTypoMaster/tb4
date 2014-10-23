<?php namespace TopBetta\Repositories;
/**
 * Coded by Oliver Shanahan
 * File creation date: 28/04/2014
 * File creation time: 5:32 PM
 * Project: tb4
 */

use TopBetta\RaceMarket;
use TopBetta\SportsMarket;
use TopBetta\SportsSelection;
use TopBetta\SportsSelectionResults;


class DbMarketsRepository extends BaseEloquentRepository {

    protected $model;

    public function __construct(RaceMarket $model){
        $this->model = $model;
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
                \Log::info('Resulting bets for market id: ' . $marketId);
                $betResultRepo = new BetResultRepo();
                $betResultRepo->resultAllSportBetsForMarket($marketId);
            } else {
                $errors[] = "Problem saving results for selection: $selectionId";
            }
        } else {
            $errors[] = "Wrong status provided: $status";
        }

        return $errors;
    }

} 