<?php namespace TopBetta\Http\Controllers\Frontend;

/**
 * Coded by Oliver Shanahan
 * File creation date: 12/03/15
 * File creation time: 09:37
 * Project: tb4
 */

use Carbon\Carbon;
use Input;
use File;
use Response;
use Request;
use Config;
use Cache;
use TopBetta\Http\Controllers\Controller;
use TopBetta\Services\Response\ApiResponse;
use TopBetta\Repositories\Contracts\SportRepositoryInterface;
use TopBetta\Repositories\Contracts\CompetitionRepositoryInterface;
use TopBetta\Repositories\Contracts\EventRepositoryInterface;
use TopBetta\Repositories\Contracts\MarketRepositoryInterface;
use TopBetta\Repositories\Contracts\SelectionRepositoryInterface;


class FeedController extends Controller {

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
        $validResources = ['sports', 'competitions', 'events'];
        if(!in_array($input['resource'], $validResources)) return $this->response->failed('Valid resource not specified', 400, 801, 'Valid resource not specified', 'resource=sports or resource=competitions query string paramater');

        // set from and to dates if not provided
        if(!isset($input['to'])) $input['to'] = Carbon::now('Australia/Sydney')->addDay()->toDateString();

        $sport = '';
        if(isset($input['sport'])) $sport = '_'.$input['sport'];

        switch($input['resource']){
            case 'sports':
                $response = $this->_getSports();
                break;


            case 'competitions':
                if(!isset($input['from'])) $input['from'] = Carbon::now('Australia/Sydney')->toDateString();

                $response = Cache::remember('topbetta-xml-feed-sports-comps_'.$input['resource'].'_'.$input['from'].'-'.substr($input['to'], 0 , 10). $sport, 1, function() use ($input)
                {
                    return $this->_getCompetitions($input);
                });

                break;

            case 'events':
                if(!isset($input['from'])) $input['from'] = Carbon::now('Australia/Sydney');

                $response = Cache::remember('topbetta-xml-feed-sports-events_'.$input['resource'].'_'.$input['from'].'-'.substr($input['to'], 0 , 10). $sport, 1, function() use ($input)
                {
                    return $this->_getEvents($input['from'], $input['to']);
                });
                break;
        }

        $ext = File::extension(Request::url());

        return Response::$ext($response);

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
            $competition['competition_url'] = Config::get('topbetta.SPORTS_LINK').'/'.$competition['competition_id'];
            // get events for competition
            $events = $this->event->getEventsforCompetitionId($competition['competition_id'], $input['from']);
            // $events = $this->_getEvents($competition['competition_id'], $input['from'], $input['to']);

            // loop on each event
            foreach($events as $event){
                $event['event_url'] = Config::get('topbetta.SPORTS_LINK').'/'.$competition['competition_id'].'/'.$event['event_id'];
                // get markets
                $markets = $this->market->getMarketsForEventId($event['event_id']);
                //$markets = $this->_getMarkets($event['event_id']);

                // loop on each market
                foreach($markets as $market){
                    $market['market_url'] = Config::get('topbetta.SPORTS_LINK').'/'.$competition['competition_id'].'/'.$event['event_id'].'/'.$market['market_id'];
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
                $response['competitions'][] = $competition;
            }
        }
        return $response;
    }


    private function _getEvents($from, $to){

        $events = $this->event->getEventsforDateRange($from, $to);
        $response = array();

        foreach($events as $event){
            $event['event_url'] = Config::get('topbetta.SPORTS_LINK').'/'.$event['competition_id'].'/'.$event['event_id'];
            // get markets
            $markets = $this->market->getMarketsForEventId($event['event_id']);
            //$markets = $this->_getMarkets($event['event_id']);

            // loop on each market
            foreach($markets as $market){
                $market['market_url'] = Config::get('topbetta.SPORTS_LINK').'/'.$event['competition_id'].'/'.$event['event_id'].'/'.$market['market_id'];
                // get selections
                $selections = $this->_getSelections($market['market_id']);
                if($selections){
                    $market['selections'] = $selections;
                    $event['markets'][] = $market;
                }

            }
            if(isset($event['markets'])){
                $response['events'][] = $event;
              //  $competition['events'][] =  $event;
            }

        }
        return $response;
    }


    private function _getMarkets($id){
        return $this->market->getMarketsForEventId($id);
    }

    private function _getSelections($id){
        return $this->selection->getSelectionsforMarketId($id);
    }

    // function defination to convert array to xml
    private function array_to_xml($responseArray, &$xml_response) {
        foreach($responseArray as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml_response->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                }
                else{
                    $subnode = $xml_response->addChild("item$key");
                    $this->array_to_xml($value, $subnode);
                }
            }
            else {
                $xml_response->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }

}