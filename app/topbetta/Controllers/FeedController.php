<?php namespace TopBetta\Controllers; 

/**
 * Coded by Oliver Shanahan
 * File creation date: 12/03/15
 * File creation time: 09:37
 * Project: tb4
 */

use BaseController;
use Carbon\Carbon;
use Input;
use TopBetta\Services\Response\ApiResponse;

use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;


class FeedController extends BaseController {

    protected $response;
    protected $sport;
    protected $competition;
    protected $event;
    protected $market;
    protected $selection;

    function __construct(ApiResponse $response,
                         SportRepositoryInterface $sport,
                         CompetitionRepositoryInterface $competition,
                         MarketRepositoryInterface $market,
                         SelectionRepositoryInterface $selection,
                         EventRepositoryInterface $event)
    {
        $this->response = $response;
        $this->sport = $sport;
        $this->competition = $competition;
        $this->event = $event;
        $this->market = $market;
        $this->selection = $selection;
    }


    public function index(){
        $input = Input::all();

        // check type
        if(!isset($input['type'])) return $this->response->failed('Type not specified', 400, 800, 'Type not included', 'Add a type=racing or type=sport query string paramater');

        // check resource
        if(!isset($input['resource'])) return $this->response->failed('Resource specified', 400, 800, 'Resource not included', 'Add a resource=sports or resource=competitions query string paramater');

        // check that type is racing or sports
        if($input['type'] != 'sport' && $input['type'] != 'racing') return $this->response->failed('Valid type not specified', 400, 801, 'Valid type not specified: '.$input['type'], 'Add a type=racing or type=sport query string paramater');

        // check that resource is valid
        $validResources = ['sports', 'competitions'];
        if(!in_array($input['resource'], $validResources)) return $this->response->failed('Valid resource not specified', 400, 801, 'Valid resource not specified', 'resource=sports or resource=competitions query string paramater');


        // set from and to dates if not provided
        if(!isset($input['from'])) $input['from'] = Carbon::now('Australia/Sydney')->toDateString();
        if(!isset($input['to'])) $input['to'] = Carbon::now('Australia/Sydney')->addDay()->toDateString();

        switch($input['resource']){
            case 'sports':
                $response = $this->_getSports();
                break;
            case 'competitions':
                $response = $this->_getCompetitions($input);
                break;
            case 'events':
                $response = $this->_getEvents($input);
                break;
        }

        return $this->response->success($response, 200);
    }

    private function _getSports(){
        return $this->sport->sportsFeed();
    }

    private function _getCompetitions($input){
        $competitions =  $this->competition->competitionFeed($input);
        $response = array();

        // loop on each competition
        foreach($competitions as $competition){

            // get events for competition
           $events = $this->_getEvents($competition['competition_id']);

            // loop on each event
            foreach($events as $event){
                // get markets
                $markets = $this->_getMarkets($event['event_id']);

                $m = array();
                // loop on each market
                foreach($markets as $market){
                    // get selections
                    $selections = $this->_getSelections($market['market_id']);
                    if($selections){
                        $market['selections'] = $selections;
                        $event['markets'][] = $market;
                    }

                }
                if(isset($event['markets'])){
                    $competition['events'][] =  $event;
                }

            }
            if(isset($competition['events'])){
                $response[] = $competition;
            }

        }
        return $response;
    }


    private function _getEvents($id){
        return $this->event->getEventsforCompetitionId($id);
    }


    private function _getMarkets($id){
        return $this->market->getMarketsForEventId($id);
    }

    private function _getSelections($id){
        return $this->selection->getSelectionsforMarketId($id);
    }

}