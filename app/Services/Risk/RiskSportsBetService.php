<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 18/05/2015
 * Time: 10:49 AM
 */

namespace TopBetta\Services\Risk;


use Carbon\Carbon;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Helpers\RiskManagerAPI;

class RiskSportsBetService extends AbstractRiskBetService {

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;

    public function __construct(BetRepositoryInterface $betRepository)
    {
        $this->betRepository = $betRepository;
    }

    public function sendBet($bet)
    {
        $bet = $this->betRepository->find($bet);

        $riskBet = array(
            'result_status' => '',
            'dividend' => 0, // TODO: what to do for multis?
            'bet_amount' => $bet->bet_amount,
            'free_bet_amount' => $bet->bet_freebet_amount,
            'placed_at' => Carbon::createFromFormat('Y-m-d H:i:s', $bet->created_at)->format(DATE_ISO8601),
            'bet_id' => (int) $bet->id,
            'client_id' => $bet->user->id,
            'client_username' => $bet->user->username,
            'client_btag' => $bet->user->topbettauser->btag,
            'sport_bet_selections' => array(),
        );

        foreach($bet->betselection as $selection) {
            $selectionDetails = $selection->selection;

            $riskBet['sport_bet_selections'][] = array(
                // Bet Selection Data - bet_selection record
                //'bet_selection_id' => '',
                //'bet_selection_dividend' => '', // is this fixed odds

                // Option Data - selection record
                'option_id' => $selection->selection_id,
                'option_name' => $selectionDetails->name,
                'option_odds' => $selection->fixed_odds * 100,  // is this fixed odds
                'option_line' => $selectionDetails->price->line,
                // 'option_bet_limit' => '', // ?

                // Market Data - market recors
                'market_id' => $selectionDetails->market_id,
                'market_status' => $selectionDetails->market_status,
                //   'market_line' => '',

                // Market Type Data
                'market_type_id' => $selectionDetails->market->market_type_id,
                'market_name' => $selectionDetails->market->marketType->name,

                // Event Data
                'event_id' => $selectionDetails->market->event_id,
                'event_name' => $selectionDetails->market->event->name,
                'event_start_time' => Carbon::createFromFormat('Y-m-d H:i:s', $selectionDetails->market->event->start_date)->toDateTimeString(),

                // Competition Data
                'competition_id' => $selectionDetails->market->event->competition->first()->id,
                'competition_name' => $selectionDetails->market->event->competition->first()->name,
                'competition_start_time' => Carbon::createFromFormat('Y-m-d H:i:s', $selectionDetails->market->event->competition->first()->start_date)->toDateTimeString(),

                // Sport Data
                'sport_id' => $selectionDetails->market->event->competition->first()->sport->id,
                'sport_name' => $selectionDetails->market->event->competition->first()->sport->name,
            );
        }

        RiskManagerAPI::sendSportsBet($riskBet);
    }
}