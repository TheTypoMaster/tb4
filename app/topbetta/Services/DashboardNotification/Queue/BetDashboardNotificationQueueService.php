<?php
/**
 * Created by PhpStorm.
 * User: Thomas Muir
 * Date: 30/03/2015
 * Time: 10:41 AM
 */

namespace TopBetta\Services\DashboardNotification\Queue;

use TopBetta\Repositories\Contracts\AccountTransactionRepositoryInterface;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;
use TopBetta\Repositories\Contracts\FreeCreditTransactionRepositoryInterface;

class BetDashboardNotificationQueueService extends AbstractTransactionDashboardNotificationService {

    const NOTIFICATION_TYPE_BET_PLACEMENT = 'bet_placement';

    const NOTIFICATION_TYPE_BET_REFUND = 'bet_refund';

    const NOTIFICATION_TYPE_BET_RESULTED = 'bet_resulted';

    /**
     * @var BetRepositoryInterface
     */
    private $betRepository;
    /**
     * @var AccountTransactionRepositoryInterface
     */
    private $accountTransactionRepository;
    /**
     * @var FreeCreditTransactionRepositoryInterface
     */
    private $freeCreditTransactionRepository;

    public function __construct(BetRepositoryInterface $betRepository,
                                AccountTransactionRepositoryInterface $accountTransactionRepository,
                                FreeCreditTransactionRepositoryInterface $freeCreditTransactionRepository)
    {
        $this->betRepository = $betRepository;
        $this->accountTransactionRepository = $accountTransactionRepository;
        $this->freeCreditTransactionRepository = $freeCreditTransactionRepository;
    }

    public function getEndpoint()
    {
        return "test-notify";
    }

    public function getHttpMethod()
    {
        return "POST";
    }


    public function formatPayload($data)
    {
        if( ! array_get($data, 'id') ) {
            \Log::error("No bet id provided for dashboard notification " . print_r($data, true));
            return array();
        }

        $bet = $this->betRepository->getBetWithSelectionsAndEventDetailsByBetId($data['id']);

        $payload = array(
            "bet_amount"           => array_get($bet, 'amount', 0),
            "bet_bonus_amount"     => array_get($bet, "bet_freebet_amount", 0),
            "bet_username"         => array_get($bet, 'user.username', null),
            "bet_resulted"         => (bool)array_get($bet, "resulted_flag", false),
            "bet_bonus_bet"        => (bool)array_get($bet, 'bet_freebet_flag', false),
            "bet_selection_string" => array_get($bet, "selection_string", null),
            "bet_type_name"        => array_get($bet, 'type.name', null),
            "external_id"          => array_get($bet, 'id', 0),
            "bet_dividend"         => null, //TODO: work out what to actually pass,
            "type"             => null,
            "transactions"         => array(),
        );

        if($betType = array_get($bet, 'type', null)) {
            $payload['type'] = array(
                "external_id" => array_get($betType, 'id', 0),
                "bet_type_name" => array_get($betType, 'name', null),
                "bet_type_description" => array_get($betType, 'description', null),
            );
        }

        //format transactions
        if(array_get($data, 'notification_type', null)) {
            $payload['transactions'] = $this->formatTransactions($bet, $data['notification_type']);
        }

        if( $betSelection = array_get($bet, 'betselection', null) ) {
            $selections = $this->formatSelections($betSelection);

            $payload = array_merge($payload, $selections);
        }

        return $payload;
    }

    /**
     * Format all of the transactions depending on the notification type.
     * @param $bet
     * @param $notificationType
     * @return array
     */
    private function formatTransactions($bet, $notificationType)
    {
        $transactions = array();

        switch($notificationType)
        {
            case self::NOTIFICATION_TYPE_BET_PLACEMENT:
                $transactions[] = $this->formatTransaction($this->accountTransactionRepository->findWithType($bet['bet_transaction_id']));
                if($bet['bet_freebet_flag']) {
                    $transactions[] = $this->formatTransaction($this->freeCreditTransactionRepository->findWithType($bet['bet_freebet_transaction_id']));
                }
                break;

            case self::NOTIFICATION_TYPE_BET_REFUND:
                $transaction[] = $this->formatTransaction($this->accountTransactionRepository->findWithType($bet['refund_transaction_id']));
                if($bet['bet_freebet_flag'] && $bet['refund_freebet_transaction_id']) {
                    $transaction[] = $this->formatTransaction($this->freeCreditTransactionRepository->findWithType($bet['refund_freebet_transaction_id']));
                }
                break;

            case self::NOTIFICATION_TYPE_BET_RESULTED:
                $transactions[] = $this->formatTransaction($this->accountTransactionRepository->findWithType($bet['result_transaction_id']));
                break;

        }

        return $transactions;
    }

    private function formatSelections($betSelections)
    {
        $selections = array();
        $runners = array();

        foreach($betSelections as $betSelection) {
            if( array_get($betSelection, 'selection.market.event.competition.0.type_code', false) ) {
                $runners[] = $this->formatRunner($betSelection['selection']);
            } else {
                $selections[] = $this->formatSelection($betSelection['selection']);
            }
        }

        $payload = array();
        if(count($selections)) $payload['selections'] = $selections;
        if(count($runners)) $payload['runners'] = $runners;

        return $payload;
    }

    private function formatMarketType($marketType)
    {
        return array (
            "external_id" => array_get($marketType, 'id', 0),
            "market_type_name" => array_get($marketType, 'name', null),
            "market_type_description" => array_get($marketType, 'description', null),
        );
    }


    private function formatRunner($selection)
    {
        $runner = array(
            "external_id" => array_get($selection, 'id', 0),
            "runner_external_selection_id" => array_get($selection, 'external_selection_id', 0),
            "runner_name" => array_get($selection, 'name', null),
            "runner_number" => array_get($selection, 'number', null),
            "runner_associate" => array_get($selection, 'associate', null),
            "runner_barrier" => array_get($selection, 'barrier', null),
            "runner_handicao" => array_get($selection, 'handicap', null),
            "runner_ident" => array_get($selection, 'ident', null),
            "runner_silk" => array_get($selection, 'silk_id', null),
            "runner_weight" => array_get($selection, 'weight', null),
            "runner_trainer" => array_get($selection, 'trainer', null),
            "runner_last_starts" => array_get($selection, 'last_starts', null),
            "runner_image_url" => array_get($selection, 'image_url', null),
            "market_type" => null,
            "race" => null,
        );

        if( $marketType = array_get($selection, 'market.markettype', null) ) {
            $runner['market_type'] = $this->formatMarketType($marketType);
        }

        if( $race = array_get($selection, 'market.event', null) ) {
            $runner['race'] = array(
                "external_id" => array_get($race, 'id', 0),
                "race_number " => array_get($race, 'number', 0),
                "race_name" => array_get($race, 'name', null),
                "race_start_date" => array_get($race, 'start_date', null),
                "race_distance" => array_get($race, 'distance', null),
                "race_class" => array_get($race, 'class', null),
                "meeting" => null,
            );

            if( $meeting = array_get($race, 'competition.0', null) ) {
                $runner['race']['meeting'] = array(
                    "external_id" => array_get($meeting, 'id', 0),
                    "meeting_code" => array_get($meeting, 'meeting_code', null),
                    "meeting_name" => array_get($meeting, 'name', null),
                    "meeting_state" => array_get($meeting, 'state', null),
                    "meeting_track" => array_get($meeting, 'track', null),
                    "meeting_weather" => array_get($meeting, 'weather', null),
                    "meeting_date" => array_get($meeting, 'start_date', null),
                    "meeting_type_code" => array_get($meeting, 'type_code', null),
                    "meeting_country" => array_get($meeting, 'country', null),
                    "meeting_grade" => array_get($meeting, 'grade', null),
                    "meeting_rail_position" => array_get($meeting, 'rail_position', null),
                    "race_type" => null,
                );

                $raceType = array_get($meeting, 'type_code', null);
                if( $raceType == "R" ) {
                    $runner['race']['meeting']['race_type'] = array(
                        "external_id" => 1,
                        "race_type_name" => "galloping"
                    );
                } else if ( $raceType == "H" ) {
                    $runner['race']['meeting']['race_type'] = array(
                        "external_id" => 2,
                        "race_type_name" => "harness"
                    );
                } else if ( $raceType == "G" ) {
                    $runner['race']['meeting']['race_type'] = array(
                        "external_id" => 3,
                        "race_type_name" => "greyhounds"
                    );
                }
            }
        }

        return $runner;
    }

    private function formatSelection($selection)
    {
        $selection = array(
            "external_id" => array_get($selection, 'id', 0),
            "selection_name" => array_get($selection, 'name', null),
            "selection_home_away" => array_get($selection, 'home_away', null),
            "market_type" => null,
            "event" => null,
        );

        if( $marketType = array_get($selection, 'market.markettype', null) ) {
            $runner['market_type'] = $this->formatMarketType($marketType);
        }

        if( $event = array_get($selection, 'market.event', null) ) {
            $selection['event'] = array(
                "external_id" => array_get($event, 'id', 0),
                "event_name" => array_get($event, 'name', null),
                "event_start_date" => array_get($event, 'start_date', null),
                "competition" => null,
            );

            if( $competition = array_get($event, 'competition.0', null) ) {
                $selection['event']['competition'] = array(
                    "external_id" => array_get($competition, 'id', 0),
                    "competition_name" => array_get($competition, 'name', null),
                    "competition_start_date" => array_get($competition, 'start_date', null),
                    "competition_country" => array_get($competition, 'country', null),
                    "sport" => null,
                );

                if( $sport = array_get($competition, 'sport', null) ) {
                    $selection['event']['competition']['sport'] = array(
                        "external_id" => array_get($sport, 'id', 0),
                        "sport_name" => array_get($sport, 'name', null),
                        "sport_description" => array_get($sport, 'description', null),
                    );
                }
            }
        }

        return $selection;
    }



}