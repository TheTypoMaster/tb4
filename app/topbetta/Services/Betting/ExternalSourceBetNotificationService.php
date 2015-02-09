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
use Log;
use Auth;

use TopBetta\Repositories\Contracts\BetSourceRepositoryInterface;
use TopBetta\Repositories\Contracts\BetRepositoryInterface;

/**
 * Notify's external sources when bets are placed and resulted
 * - Bet's Placed
 * -- bet details are retrived from the database
 * -- relevant data is extracted and payload is formatted for external API
 * -- payload is put in a job on a queue
 * -- email alerts are sent if there is an issue pushing bets to the API
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
     * Notify external bet source of bet placement
     *
     * @param $betSourceId
     * @param $betDetails
     * @return null
     */
    public function notifyBetPlacement($betSourceId, $betDetails)
    {
        // get the api endpoint for the source
        if(!$betSourceDetails = $this->_sourceValidation($betSourceId)) return false;

        // get bet details
        $betDetailsFromDB = $this->_getbetDetails($betDetails[0]['bet_id']);

        // get logged in user details
        $bettinguserName = Auth::user()->username;

        // set username to the child betting user
        $betDetailsFromDB['betting_username'] = $bettinguserName;

        // format the payload
        $BetDetailsPayload = $this->_formatBetPayload($betDetailsFromDB);

        // put it on the queue to be sent
        return $this->_queueJob($betSourceDetails, $BetDetailsPayload);

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
     * Vaidate the external source and check that it has an API endpoint to push bet information to
     *
     * @param $betSourceId
     * @return bool
     */
    private function _sourceValidation($betSourceId)
    {
        // get bet source record
        if(!$betSourceDetails = $this->source->find($betSourceId)) return false;

        // make sure the record has an endpoint
        if(!$betSourceDetails['api_endpoint']) return false;

        // return api endpoint
        return $betSourceDetails;
    }

    /**
     * Get the details of the bet and it's related resources
     *
     * @param $betId
     * @return mixed
     */
    private function _getbetDetails($betId){
        return $this->bet->getBetWithSelectionsByBetid($betId);
    }

    /**
     * Extracts and formats the bet information from the database that will be pushed to the API
     *
     * @param $betDetails
     * @return array
     */
    private function _formatBetPayload($betDetails){

        $betSelections =  array();

        // extract selecitons
        foreach ($betDetails['betselection'] as $betSelection) {
            $betSelectionPayload = array();
            // always available
            $betSelectionPayload['bet_selection_external_id'] =  (int) $betSelection['selection']['id'];
            $betSelectionPayload['bet_selection_competition'] =  $betSelection['selection']['market']['event']['competition'][0]['name'];
            $betSelectionPayload['bet_selection_event'] =  $betSelection['selection']['market']['event']['name'];
            $betSelectionPayload['bet_selection_market'] =  $betSelection['selection']['market']['markettype']['name'];
            $betSelectionPayload['bet_selection_name'] =  $betSelection['selection']['name'];
            $betSelectionPayload['bet_selection_placed_odd'] = $betSelection['fixed_odds'];
            //$betSelectionPayload['bet_selection_resulted'] = $betSelection['status']['name'];

            // sometimes available
            if(isset($betSelection['selection']['market']['event']['competition']['sport'])){
                $betSelectionPayload['bet_selection_sport'] =  $betSelection['selection']['market']['event']['competition']['sport']['name'];
            } else {
                $betSelectionPayload['bet_selection_sport'] = 'Racing';
            }

            if(isset($betSelection['selection']['result']['win_dividend']) && $betSelection['bettype']['name'] == 'win')
                $betSelectionPayload['bet_selection_dividend'] = $betSelection['selection']['result']['win_dividend'];
            if(isset($betSelection['selection']['result']['place_dividend']) && $betSelection['bettype']['name'] == 'place')
                $betSelectionPayload['bet_selection_dividend'] = $betSelection['selection']['result']['place_dividend'];

            $betSelectionPayload['bet_selection_resulted'] = 0;
            $betSelectionPayload['bet_selection_dividend'] = "";
            $betSelectionPayload['bet_selection_win'] = 0;

            $betSelections[] = $betSelectionPayload;
        }

        $payloadArray = array();

         // always available
        $payloadArray['bet_external_bet_id'] = (int) $betDetails['id'];
        $payloadArray['bet_type'] = $betDetails['type']['name'];
        $payloadArray['bet_amount'] = (int) $betDetails['bet_amount'];
        $payloadArray['bet_status'] = $betDetails['status']['name'];
        $payloadArray['bet_source'] = $betDetails['source']['keyword'];
        $payloadArray['bet_username'] = $betDetails['betting_username'];

        // default values ?
        $payloadArray['bet_multi'] = 0;
        $payloadArray['bet_manual'] = 0;

        // sometimes available
        if(isset($betDetails['result'])) $payloadArray['bet_collect_amount'] = $betDetails['result']['amount'];

        // add selections
        $payloadArray['bet_selections'] = $betSelections;

        return $payloadArray;
    }

    private function _queueJob($betSourceDetails, $formattedBetDetails){

        $data = array('parameters' => array('source_details' => $betSourceDetails, 'notification' => 'email', 'request_type' => 'HTTP'), 'bet_data' => $formattedBetDetails);

        Log::debug('Bet Notification: About to queue job');
        Queue::push('\TopBetta\Services\Betting\ExternalSourceBetNotifcationQueueService', $data, 'bet-notification');

    }
}