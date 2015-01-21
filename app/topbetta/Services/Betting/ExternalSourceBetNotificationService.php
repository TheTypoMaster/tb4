<?php namespace TopBetta\Services\Betting;

/**
 * Coded by Oliver Shanahan
 * File creation date: 17/01/15
 * File creation time: 08:51
 * Project: tb4
 */

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Queue;

use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;

/**
 * Notify's external sources when bets are placed and resulted
 *
 * Class ExternalSourceBetNotificationService
 * @package TopBetta\Services
 */

class ExternalSourceBetNotificationService {

    protected $source;
    protected $bet;

    function __construct(BetSourceRepositoryInterface $source,
                         BetRepositoryInterface $bet)
    {
        $this->source = $source;
        $this->bet = $bet;
    }


    /**
     * Notify bet source of bet placement
     *
     * @param $betSourceId
     * @param $betDetails
     * @return null
     */
    public function notifyBetPlacement($betSourceId, $betDetails)
    {
        // get the api endpoint for the source
        if(!$apiEndpoint = $this->_sourceValidation($betSourceId)) return false;

        // get the bet details for the bet
       //  if(!$betDetailsFromDB = $this->_getbetDetails($betDetails[0]['bet_id'])) return false;

        $betDetailsFromDB = $this->_getbetDetails($betDetails[0]['bet_id']);

    //    dd($betDetailsFromDB);

        // format the payload
        $BetDetailsPayload = $this->_formatBetPayload($betDetailsFromDB);

        return $BetDetailsPayload;
        // put it on the queue to be sent
        $this->_queueJob($apiEndpoint, $BetDetailsPayload);

    }

    /**
     * Notify bet source of bet result
     *
     * @param $betSourceId
     * @param $betDetails
     */
    public function notifyBetResult($betSourceId, $betDetails)
    {

    }

    /**
     * Vaidate the external source is valid and has an API endpoint to push bet information to
     *
     * @param $betSourceId
     * @return bool
     */
    private function _sourceValidation($betSourceId)
    {
        // get bet source record
        if(!$betSourceDetails = $this->source->find($betSourceId)) return false;

        // make sure the record has an endpoint
        if(!$sourceApiEndpoint = $betSourceDetails['api_endpoint']) return false;

        // return api endpoint
        return $sourceApiEndpoint;
    }

    private function _getbetDetails($betId){
        return $this->bet->getBetWithSelectionsByBetid($betId);
    }

    private function _formatBetPayload($betDetails){

        $betSelections = array();

        // extract selecitons
        foreach ($betDetails['betselection']['selection'] as $selection) {
            $betSelections[]['bet_selection_external_id'] = $selection['id'];
            $betSelections[]['bet_selection_sport'] =  $selection['market']['event']['competition']['sport']['name'];
            $betSelections[]['bet_selection_competition'] =  $selection['market']['event']['competition']['name'];
            $betSelections[]['bet_selection_event'] =  $selection['market']['event']['name'];
            $betSelections[]['bet_selection_market'] =  $selection[''];
            $betSelections[]['bet_selection_name'] =  $selection['name'];
            $betSelections[]['bet_selection_placed_odd'] =  $selection[''];
            $betSelections[]['bet_selection_resulted'] =  $selection[''];
            $betSelections[]['bet_selection_dividend'] =  $selection[''];
            $betSelections[]['bet_selection_win'] =  $selection[''];
        }


        $payloadArray = array('bet_external_bet_id' => $betDetails['id'],
                                'bet_multi' => 0,
                                'bet_amount' => $betDetails['bet_amount'],
                                'bet_status' => $betDetails['bet_amount'],
                                'bet_source' => 'topbetta',
                                'bet_manual' => 0,
                                'bet_username' => $betDetails['username'],
                                'bet_collect_amount' => '',
                                'bet_selections' => $betSelections);


       return $betDetails;

    }

    private function _queueJob($sourceApiEndpoint, $formattedBetDetails){

        $data = array('parameters' => array('api_endpoint' => $sourceApiEndpoint, 'notification' => 'email'), 'bet_data' => $formattedBetDetails);

        Queue::push('\TopBetta\Services\Betting\ExternalSourceBetNotifcationQueueService', $data, 'bet-notification-api');

    }


}