<?php

/**
 * @version		$Id: betting.php
 * @package		API
 * @subpackage
 * @copyright	Copyright (C) 2012 TopBetta. All rights reserved.
 * */
jimport('joomla.application.component.controller');
jimport('mobileactive.wagering.bet');
jimport('mobileactive.wagering.api');

class Api_Betting extends JController
{

    function Api_Betting()
    {
        // TODO: load these classes
        // libraries/mobileactive/application/utilities/format.php:class Format
        // libraries/mobileactive/wagering/bet.php
    }
	
    /*
     *
     * MAPS TO: /com_betting/controller.php->display
     * WITH: /com_betting/views/betting/view.html.php->listView
     */

    public function getRacingByType()
    {

        if ($type = RequestHelper::validate('type')) {

            /*
             * TYPE (open_id):
             * 	R - Galloping
             *  G - Greyhounds
             *  H - harness
             *  A - All (not implimented yet!!)
             */
            $type = strtoupper($type);

            $component_list = array('tournament', 'topbetta_user');
            foreach ($component_list as $component) {
                $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
                $this->addModelPath($path);
            }

            $meeting_model = &$this->getModel('Meeting', 'TournamentModel');
            $race_model = &$this->getModel('Race', 'TournamentModel');
            $race_status_model = &$this->getModel('EventStatus', 'TournamentModel');
            $result_model = &$this->getModel('SelectionResult', 'TournamentModel');
            $runner_model = &$this->getModel('Runner', 'TournamentModel');

            $meeting_list = $meeting_model->getTodayActiveMeetingList();

            foreach ($meeting_list as $meeting) {
                // Only fetch the meetings we are intereted in
                if ($meeting->type_code == $type) {
                    $meeting->race_list = $race_model->getRaceListByMeetingID($meeting->id);
                }
            }

            /* >> BUILD OUR OUTPUT << */
            $meeting_race_list = array('galloping' => array(), 'greyhounds' => array(), 'harness' => array(),);

            //TODO: fix these 3 models
            //$race_status_model = &$this -> getModel('EventStatus');
            //$result_model = &$this -> getModel('SelectionResult');
            //$runner_model = &$this -> getModel('Runner');

            $abandoned_status = $race_status_model->getEventStatusByKeyword('abandoned');
            $selling_status = $race_status_model->getEventStatusByKeyword('selling');

            // why is this preset?
            $meeting_race_limit = array('galloping' => 10, 'harness' => 10, 'greyhounds' => 12,);

            if (!empty($meeting_list)) {
                foreach ($meeting_list as $meeting) {
                    $race_list = array();
                    $next_race_marked = false;
                    if (!empty($meeting->race_list)) {
                        foreach ($meeting->race_list as $race) {

                            $start_time = strtotime($race->start_date);
                            $abandoned = ($race->event_status_id == $abandoned_status->id);

                            $tips_title = 'Race No. ' . $race->number . ' &ndash; ' . date('g:ia', $start_time);
                            $tips_body = $race->name;

                            if ($abandoned) {
                                $label = '--';
                                $class = ($start_time > time() ? 'raceFuture' : 'racePast');
                            } else if ($start_time > time()) {
                                $label = Format::counterText($start_time);
                                //format the time display
                                $label = str_replace(' hr', 'h', $label);
                                $label = str_replace(' min', 'm', $label);
                                $label = str_replace(' sec', 's', $label);
                                $class = ($next_race_marked ? 'raceFuture' : 'racePresent');
                                $next_race_marked = true;
                            } else if ($race->event_status_id != $selling_status->id) {

                                $label = 'pending';
                                $class = 'racePast';

                                $result_list = $result_model->getSelectionResultListByEventID($race->id);

                                if (!empty($result_list)) {
                                    $runner_list = $runner_model->getRunnerListByRaceID($race->id);
                                    $runner_list_by_id = array();
                                    foreach ($runner_list as $runner) {
                                        $runner_list_by_id[$runner->id] = $runner;
                                    }
                                    $result_display_list = $this->_getResultDisplayList($result_list, $runner_list_by_id, $race);

                                    $rank_display = array();
                                    foreach ($result_display_list['rank'] as $result) {
                                        if ($result['rank_no'] < 4) {
                                            $rank_display[$result['rank_no']][] = $result['number'];
                                        }
                                    }
                                    ksort($rank_display);

                                    $j = 0;
                                    foreach ($rank_display as $rank => $numbers) {
                                        $rank_count = count($numbers);

                                        if ($rank_count > 1) {
                                            $rank_display[$rank] = '(' . implode(', ', $numbers) . ')';
                                        } else {
                                            $rank_display[$rank] = $numbers[0];
                                        }
                                        $j += count($numbers);

                                        if ($j > 3) {
                                            break;
                                        }
                                    }

                                    $label = implode(', ', $rank_display);
                                    $class = 'racePast';

                                    //$tips_body .= $this -> _formatResultTipsBody($result_display_list);
                                    $tips_body .= $result_display_list;

                                    //$tips_body .= '<table class="bet_link_tips-result"><tr><td>col1</td><td>col2</td></tr></table>';
                                }
                            } else {
                                $label = 'pending';
                                $class = 'racePast';
                            }

                            $race->weather = (empty($race->weather) ? 'N/A' : $race->weather);
                            $race->track_condition = (empty($race->track_condition) ? 'N/A' : $race->track_condition);

                            $race_list[$race->number] = array('link' => OutputHelper::api_link('getRaceDetails', "meet={$meeting->id}&racenum={$race->number}"), 'label' => $label, 'class' => $class, 'tips_title' => $tips_title, 'tips_body' => $tips_body, 'distance' => $race->distance, 'weather' => $race->weather, 'track' => $race->track_condition);
                        }
                    }
                    $competition_name = strtolower($meeting->competition_name);

                    // filter out the other types we don't need
                    if ($meeting->type_code == $type) {
                        $meeting_race_list[$competition_name][$meeting->id] = array('meeting_id' => $meeting->id, 'meeting_name' => $meeting->name . ' (' . $meeting->state . ')', 'weather' => $meeting->weather, 'track' => $meeting->track, 'race_list' => $race_list);

                        //sort the meeting as per the completation
                        $meeting_race_future = $meeting_race_completed = $meeting_race_list_new = array();
                        foreach ($meeting_race_list as $ids => $meetings) {
                            foreach ($meetings as $meeting) {
                                if (is_array($meeting['race_list']) && isset($meeting['race_list'][count($meeting['race_list'])])) {
                                    if ($meeting['race_list'][count($meeting['race_list'])]['class'] == 'racePast') {
                                        $meeting_race_completed[$ids][$meeting['meeting_id']] = array('meeting_id' => $meeting['meeting_id'],
                                            'meeting_name' => $meeting['meeting_name'],
                                            'weather' => $meeting['weather'],
                                            'track' => $meeting['track'],
                                            'race_list' => $meeting['race_list']);
                                    } else {
                                        $meeting_race_future[$ids][$meeting['meeting_id']] = array('meeting_id' => $meeting['meeting_id'],
                                            'meeting_name' => $meeting['meeting_name'],
                                            'weather' => $meeting['weather'],
                                            'track' => $meeting['track'],
                                            'race_list' => $meeting['race_list']);
                                    }
                                }
                            }
                        }


                        foreach ($meeting_race_future as $id => $val)
                            foreach ($val as $val1)
                                $meeting_race_list_new[$id][$val1['meeting_id']] = $val1;
                        foreach ($meeting_race_completed as $id => $val)
                            foreach ($val as $val1)
                                $meeting_race_list_new[$id][$val1['meeting_id']] = $val1;

                        //get the race count only for this type as well
                        //looks like it's preset above for some reason
                        //does this have any affect?
                        $race_count = count($race_list);
                        if ($race_count > $meeting_race_limit[$competition_name]) {
                            $meeting_race_limit[$competition_name] = $race_count;
                        }
                    }
                }
            }
            //MC $this -> assign('header', 'TODAY\'S RACING &ndash; ' . strtoupper(date('l jS F Y')));
            //MC $this -> assign('meeting_race_list', $meeting_race_list);
            //MC $this -> assign('meeting_race_limit', $meeting_race_limit);

            $data = array('header' => 'TODAY\'S RACING &ndash; ' . strtoupper(date('l jS F Y')), 'meeting_race_list' => $meeting_race_list_new, 'meeting_race_limit' => $meeting_race_limit);

            /*
              $view = &$this -> getView('Betting', 'html', 'BettingView');
              $view -> assign('meeting_list', $meeting_list);
              $view -> assign('open_id', JRequest::getVar('open_id', 0));

              $view -> setModel($result_model);
              $view -> setModel($runner_model);
              $view -> setModel($race_status_model);

              $view -> display();
             *
             */

            //OutputHelper::_debug($meeting->race_list);

            $result = OutputHelper::json(200, $data);
        } else {
            $result = OutputHelper::json(500, array('error_msg' => 'Not a valid race type!'));
        }

        return $result;
    }

    /*
     * getRaceDetails()
     *
     * Gets full details for a race meet and number
     * MAPS TO: /com_betting/controller->meeting
     */

    public function getRaceDetails()
    {

        $session = &JFactory::getSession();

        $component_list = array('betting', 'tournament', 'topbetta_user');
        foreach ($component_list as $component) {
            $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
            $this->addModelPath($path);
        }

        foreach (array('error', 'message') as $msg_type) {
            $sess_msg = 'sess_' . $msg_type;
            $betting_error = stripslashes($session->get($sess_msg, null, 'betting'));
            if ($betting_error) {
                JFactory::getApplication()->enqueueMessage(JText::_($betting_error), $msg_type);
            }
            $session->clear($sess_msg, 'betting');
        }

        $meeting_id = JRequest::getVar('meet', null);
        if (is_null($meeting_id)) {

            $result = "";
        }

        $meeting_model = &$this->getModel('Meeting', 'TournamentModel');
        $meeting = $meeting_model->getMeeting($meeting_id);
        $meeting_venue = $meeting->name;
        $meeting_weather = $meeting->weather;
        $meeting_track = $meeting->track;
        if (is_null($meeting)) {
            $result = "";
        }

        $competition_model = &$this->getModel('TournamentCompetition', 'TournamentModel');
        $competition = $competition_model->getTournamentCompetition($meeting->tournament_competition_id);

        $meeting->type = $competition->name;

        $race_model = &$this->getModel('Race', 'TournamentModel');

        $number = JRequest::getVar('racenum', $race_model->getNextRaceNumberByMeetingID($meeting->id));
        if (is_null($number)) {
            $number = $race_model->getLastRaceNumberByMeetingID($meeting->id);
        }

        $race = $race_model->getRaceByMeetingIDAndNumber($meeting->id, $number);

        if (is_null($race)) {
            $result = "";
            return OutputHelper::json(500, array('error_msg' => 'No race data available'));
        }

        $status_model = &$this->getModel('EventStatus', 'TournamentModel');
        $status = $status_model->getEventStatus($race->event_status_id);

        $race->status = $status->name;

        $race_time_list = $race_model->getRaceTimesByMeetingID($meeting->id);

        $runner_model = &$this->getModel('Runner', 'TournamentModel');
        $runner_list = $runner_model->getRunnerListByRaceID($race->id);

        //OutputHelper::_debug($runner_list);
        //exit;

        $runner_list_by_id = array();
        $runner_list_by_number = array();
        $runner_ident_list = array();

        $image_root = getcwd();
        $image_root = str_replace('/api', '', $image_root);

        foreach ($runner_list as $runner) {
            if ($meeting->type_code == 'G') {
                $runner_list[$runner->number]->silk_id = (file_exists($image_root . '/rugs/' . $runner->number . '.png')) ? "/rugs/" . $runner->number . ".png" : "/rugs/default.png";
            } else {
                $runner_list[$runner->number]->silk_id = (file_exists($image_root . '/silks/' . $runner->silk_id . '.png')) ? "/silks/" . $runner->silk_id . ".png" : "/silks/default.png";
            }
            $runner_list_by_id[$runner->id] = $runner;
            $runner_list_by_number[$runner->number] = $runner;
            $runner_ident_list[] = $runner->ident;
        }


        $rating_list = array();
        $rating_list = $runner_model->getRunnerRatings($runner_ident_list);

        $runner_count = 0;

        foreach ($runner_list as $runner) {
            $runner->rating = isset($rating_list[$runner->ident]) ? $rating_list[$runner->ident]->rating : 0;

            if ($runner->status == 'Not Scratched') {
                $runner_count++;
            }
        }

        $bet_type_model = &$this->getModel('BetType', 'BettingModel');
        $bet_type_list = $bet_type_model->getBetTypesByStatus(1, 'racing');
        // default is enabled

        if ($runner_count <= 4) {
            $bet_type_list = array();
            $bet_type_list[] = $bet_type_model->getBetTypeByName('win');

            switch ($runner_count) {
                case 4 :
                    $bet_type_list[] = $bet_type_model->getBetTypeByName('firstfour');
                case 3 :
                    $bet_type_list[] = $bet_type_model->getBetTypeByName('quinella');
                    $bet_type_list[] = $bet_type_model->getBetTypeByName('exacta');
                    $bet_type_list[] = $bet_type_model->getBetTypeByName('trifecta');
                    break;
            }
        }

        $user = &JFactory::getUser();

        $wagering_bet_list = array();
        $bet_list = array();

        $result_model = &$this->getModel('SelectionResult', 'TournamentModel');
        $result_list = $result_model->getSelectionResultListByEventID($race->id);


        $togo = $this->formatCounterText(strtotime($race->start_date));
        $description = $race->name;
        $race->weather = (!empty($race->weather)) ? $race->weather : 'N/A';
        $race->track_condition = (!empty($race->track_condition)) ? $race->track_condition : 'N/A';

        // get all races for this meeting
        $meeting_race_list2 = $race_model->getRaceListByMeetingID($meeting->id);
        foreach ($meeting_race_list2 as $race2) {

            $start_time = strtotime($race2->start_date);
            $abandoned = ($race2->event_status_id == $abandoned_status->id);

            $tips_title = 'Race No. ' . $race2->number . ' &ndash; ' . date('g:ia', $start_time);
            $tips_body = $race2->name;

            if ($abandoned) {
                $label = '--';
                $class = ($start_time > time() ? 'raceFuture' : 'racePast');
            } else if ($start_time > time()) {
                $label = Format::counterText($start_time);
                //format the time display
                $label = str_replace(' hr', 'h', $label);
                $label = str_replace(' min', 'm', $label);
                $label = str_replace(' sec', 's', $label);
                $class = ($next_race_marked ? 'raceFuture' : 'racePresent');
                $next_race_marked = true;
            } else {

                $label = 'pending';
                $class = 'racePast';


                $result_list2 = $result_model->getSelectionResultListByEventID($race2->id);

                if (!empty($result_list2)) {
                    $runner_list2 = $runner_model->getRunnerListByRaceID($race2->id);
                    $runner_list_by_id = array();
                    foreach ($runner_list2 as $runner2) {
                        $runner_list_by_id[$runner2->id] = $runner2;
                    }
                    $result_display_list2 = $this->_getResultDisplayList($result_list2, $runner_list_by_id, $race2);

                    $rank_display = array();
                    foreach ($result_display_list2['rank'] as $result) {
                        if ($result['rank_no'] < 4) {
                            $rank_display[$result['rank_no']][] = $result['number'];
                        }
                    }
                    ksort($rank_display);

                    $j = 0;
                    foreach ($rank_display as $rank => $numbers) {
                        $rank_count = count($numbers);

                        if ($rank_count > 1) {
                            $rank_display[$rank] = '(' . implode(', ', $numbers) . ')';
                        } else {
                            $rank_display[$rank] = $numbers[0];
                        }
                        $j += count($numbers);

                        if ($j > 3) {
                            break;
                        }
                    }

                    $label = implode(', ', $rank_display);
                    $class = 'racePast';

                    //$tips_body .= $this -> _formatResultTipsBody($result_display_list);
                    $tips_body .= $result_display_list2;

                    //$tips_body .= '<table class="bet_link_tips-result"><tr><td>col1</td><td>col2</td></tr></table>';
                }
            }

            $race2->weather = (empty($race2->weather) ? 'N/A' : $race2->weather);
            $race2->track_condition = (empty($race2->track_condition) ? 'N/A' : $race2->track_condition);

            $race_list[$race2->number] = array('link' => OutputHelper::api_link('getRaceDetails', "meet={$meeting->id}&racenum={$race2->number}"), 'label' => $label, 'class' => $class, 'tips_title' => $tips_title, 'tips_body' => $tips_body, 'distance' => $race2->distance, 'weather' => $race2->weather, 'track' => $race2->track_condition);
        }

        $data = array('meeting_venue' => $meeting_venue, 'description' => $description, 'meeting_id' => $meeting_id, 'weather' => $race->weather, 'track' => $race->track_condition, 'race_id' => $race->id, 'event_id' => $race->event_id, 'race_number' => $number, 'togo' => $togo, 'distance' => $race->distance, 'bet_type_list' => $bet_type_list, 'result_list' => $result_list, 'runner_list' => $runner_list, 'race_list' => $race_list);

        /*
          if (is_null($view)) {
          $view = &$this -> getView('Betting', 'html', 'BettingView');
          $layout = JRequest::getVar('layout', 'meeting');
          $view -> setLayout($layout);
          }

          $view -> assignRef('bet_type_list', $bet_type_list);
          $view -> assignRef('competition', $competition);
          $view -> assignRef('meeting', $meeting);
          $view -> assignRef('race', $race);

          $view -> assignRef('result_list', $result_list);

          $view -> assignRef('race_time_list', $race_time_list);

          $view -> assignRef('runner_list', $runner_list);
          $view -> assignRef('runner_list_by_id', $runner_list_by_id);
          $view -> assignRef('runner_list_by_number', $runner_list_by_number);

          $this -> _setBetListView($view, $user, $race);

          $view -> display();
         */
        //OutputHelper::_debug($data);
        $result = OutputHelper::json(200, $data);

        return $result;
    }

    /*
     * function getFastBetRaces()
     *
     * Gets next (x) to jump for fast bet
     */

    public function getFastBetRaces()
    {

        //$next_to_jump_list = array('galloping' => $this -> _getNextToJump('galloping', 5), 'harness' => $this -> _getNextToJump('harness', 5), 'greyhounds' => $this -> _getNextToJump('greyhounds', 5), );

        $next_to_jump_list = array('races' => $this->_getNextToJump('', 10));

        $result = OutputHelper::json(200, $next_to_jump_list);

        return $result;
    }

    private function _getNextToJump($meeting_type = null, $limit = null)
    {

        $meeting_type_id = null;
        if (!empty($meeting_type)) {
            if (!class_exists('TournamentModelTournamentCompetition')) {
                JLoader::import('tournamentcompetition', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
            }
            $competition_model = JModel::getInstance('TournamentCompetition', 'TournamentModel');
            $competition = $competition_model->getCompetitionByName($meeting_type);
            $meeting_type_id = $competition->id;
        }

        if (!class_exists('TournamentModelRace')) {
            JLoader::import('race', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
            JLoader::import('meeting', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
            JLoader::import('runner', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models');
        }

        $race_model = JModel::getInstance('Race', 'TournamentModel');

        $races = $race_model->getTodayNextRaceListByMeetingTypeID($meeting_type_id, $limit);

        // only send the info back we are interested in
        $results = array();
        foreach ($races as $race) {

            $meeting_model = &$this->getModel('Meeting', 'TournamentModel');
            $meeting = $meeting_model->getMeeting($race->meeting_id);

            $race_model = &$this->getModel('Race', 'TournamentModel');

            $race_list = $race_model->getRaceByMeetingIDAndNumber($meeting->id, $race->number);

            //OutputHelper::_debug($race_list);
            //exit;

            $runner_model = &$this->getModel('Runner', 'TournamentModel');
            $runner_list = $runner_model->getRunnerListByRaceID($race_list->id);
            //TODO: do we need to filter out the runner list?

            $image_root = getcwd();
            $image_root = str_replace('/api', '', $image_root);

            foreach ($runner_list as $runner) {
                if ($meeting->type_code == 'G') {
                    $runner_list[$runner->number]->silk_id = (file_exists($image_root . '/rugs/' . $runner->number . '.png')) ? "/rugs/" . $runner->number . ".png" : "/rugs/default.png";
                } else {
                    $runner_list[$runner->number]->silk_id = (file_exists($image_root . '/silks/' . $runner->silk_id . '.png')) ? "/silks/" . $runner->silk_id . ".png" : "/silks/default.png";
                }
            }

            $results[] = array('meeting_type' => $race->competition_name,
                'meeting_name' => $race->meeting_name,
                'meeting_id' => $race->meeting_id,
                'race_id' => $race->id,
                'event_id' => $race->event_id,
                'link' => OutputHelper::api_link('getRaceDetails', "meet={$race->meeting_id}&racenum={$race->number}"),
                'race_number' => $race->number,
                'runner_list' => $runner_list);
        }

        return $results;
    }

    /**
     * Get race result html used in tips
     *
     * @param $display_result_list
     * @return void
     */
    private function _formatResultTipsBody($display_result_list)
    {
        $result_tips = '<table class="bet_link_tips-result"><tr><td class="bet_link_tips-result-left">';
        $result_tips .= '<table>';
        foreach ($display_result_list['rank'] as $result) {
            $result_tips .= '<tr>';
            $result_tips .= '<td>' . $result['number'] . '- ';
            if ($result['win_dividend']) {
                $result_tips .= $result['win_dividend'] . ',&nbsp;&nbsp;';
            }
            $result_tips .= '</td>';

            $result_tips .= '<td>';
            $result_tips .= $result['place_dividend'];
            $result_tips .= '</td>';
            $result_tips .= '</tr>';
        }
        $result_tips .= '</table>';
        $result_tips .= '</td><td>';
        foreach ($display_result_list['exotic'] as $type => $exotic_result) {
            if (!empty($exotic_result) && is_array($exotic_result)) {
                foreach ($exotic_result as $combos => $dividend) {
                    $result_tips .= $combos;
                    $result_tips .= '&nbsp;&nbsp;' . $dividend;
                    $result_tips .= '<br />';
                }
            }
        }
        $result_tips .= '</td></table>';

        return $result_tips;
    }

    /**
     * Get the result list for display
     *
     * @param $result_list
     * @param $runner_list
     * @return void
     */
    protected function _getResultDisplayList($result_list, $runner_list, $race = null)
    {
        if (is_null($race)) {
            $race = $this->race;
        }

        $display_result_list = array('dividend_field' => 'odds', // for old data before dividends fields introduced
            'has_exotics' => false, 'rank' => array(), 'exotic' => array('quinella' => array(), 'exacta' => array(), 'trifecta' => array(), 'firstfour' => array(),),);
        foreach ($result_list as $result) {
            $runner = $runner_list[$result->selection_id];
            $runner_number = $runner->number;
            $win_odds = null;
            $place_odds = null;
            $win_dividend = null;
            $place_dividend = null;

            if ($result->position < 4) {
                $place_odds = $runner->place_odds;
                $place_dividend = $result->place_dividend;
            }

            if (1 == $result->position) {
                $win_odds = $runner->win_odds;
                $win_dividend = $result->win_dividend;

                if ($win_dividend > 0) {
                    $display_result_list['dividend_field'] = 'dividend';
                }
            }

            $display_result_list['rank'][] = array('rank_no' => $result->position,
                'position' => Format::ordinalNumber($result->position), 'number' => $runner->number, 'name' => $runner->name, 'win_odds' => $win_odds, 'place_odds' => $place_odds, 'win_dividend' => $win_dividend, 'place_dividend' => $place_dividend);
        }

        /*
          $wagering_bet	= WageringBet::newBet();
          foreach ($display_result_list['exotic'] as $exotic_type => $exotic_list) {
          $dividends = unserialize($race->{$exotic_type . '_dividend'});
          $display_result_list['exotic'][$exotic_type] = $dividends;

          if ($dividends > 0) {
          $display_result_list['has_exotics'] = true;
          }
          }
         *
         */
        return $display_result_list;
    }

    public function saveTournamentTicket($iframe = FALSE, $tourn_id = FALSE)
    {
        global $mainframe;

        //this is for external auth check
        $token_key = JRequest::getString('tb_key', null, 'post');
        $token_secret = JRequest::getString('tb_secret', null, 'post');
        $freeCreditFlag = (float) JRequest::getVar('chkFreeBet', 0);

        $api_user = new Api_User();
        $token = $api_user->get_external_website_key_secret($token_key, $token_secret);

        // first validate a legit token has been sent
        $server_token = JUtility::getToken();

        //$postVars = print_r(JRequest::get('GET'), true);
        //file_put_contents('/tmp/igas_tourn_ticket.log', "POST Vars:".$postVars . "\nFreeCreditFlag:$freeCreditFlag\n", FILE_APPEND | LOCK_EX);

        if (JRequest::getVar($server_token, FALSE, '', 'alnum') || $token || $iframe) {

            $component_list = array('betting', 'tournament', 'tournament_dollars', 'topbetta_user', 'payment');
            foreach ($component_list as $component) {
                $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
                $this->addModelPath($path);
            }

            // Joomla userid is being passed from Laravel
            // this fixes Joomla forgetting who is logged in :-)
            $l_user_id = JRequest::getVar('l_user_id', NULL);

            if ($l_user_id) {
                $user = & JFactory::getUser($l_user_id);
            } else {
                $user = & JFactory::getUser();
            }
            // $user =& JFactory::getUser();

            if (!$user->id) {
                return OutputHelper::json(401, array('error_msg' => 'Please login first'));
            }

            if (!class_exists('TopbettaUserModelTopbettaUser')) {
                JLoader::import('topbettauser', JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models');
            }

            if (!class_exists('TournamentdollarsModelTournamenttransaction')) {
                JLoader::import('tournamenttransaction', JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models');
            }
            $payment_dollars_model = JModel::getInstance('Accounttransaction', 'PaymentModel');

            if (!class_exists('PaymentModelAccounttransaction')) {
                JLoader::import('accounttransaction', JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models');
            }
            $tournament_dollars_model = JModel::getInstance('Tournamenttransaction', 'TournamentdollarsModel');

            if ($tourn_id) {
                $id = $tourn_id;
            } else {
                $id = JRequest::getVar('id', null);
            }
            $sport_model = & $this->getModel('TournamentSport', 'TournamentModel');
            $isRacing = $sport_model->isRacingByTournamentId($id);

            if ($isRacing > 0) {
                $tournament_model = & $this->getModel('TournamentRacing', 'TournamentModel');
                $tournament = $tournament_model->getTournamentRacingByTournamentID($id);


                if($tournament->closed_betting_on_first_match_flag){
                    if (strtotime($tournament->betting_closed_date) < time()) {
                        //return $this->ticketError(JText::_('Betting has already closed'), $save, $tournament);
                        if ($iframe) {
                            return array('status' => 500, 'error_msg' => 'Betting has already closed');
                        } else {
                            return OutputHelper::json(500, array('error_msg' => 'TournamentBetready closed. :'));
                        }
                    }
                }

            } else {
                $tournament_model = & $this->getModel('TournamentSportEvent', 'TournamentModel');
                $tournament = $tournament_model->getTournamentSportsByTournamentID($id);

                if (strtotime($tournament->betting_closed_date) < time()) {
                    //return $this->ticketError(JText::_('Betting has already closed'), $save, $tournament);
                    if ($iframe) {
                        return array('status' => 500, 'error_msg' => 'Betting has already closed');
                    } else {
                        return OutputHelper::json(500, array('error_msg' => 'Betting has already closed'));
                    }
                }
            }

            if (strtotime($tournament->end_date) < time()) {
                //return $this->ticketError(JText::_('Tournament has already finished'), $save, $tournament);
                if ($iframe) {
                    return array('status' => 500, 'error_msg' => 'Tournament has already finished');
                } else {
                    return OutputHelper::json(500, array('error_msg' => 'Tournament has already finished'));
                }
            }
            if ($tournament->cancelled_flag) {
                //return $this->ticketError(JText::_('Tournament has cancelled'), $save, $tournament);
                if ($iframe) {
                    return array('status' => 500, 'error_msg' => 'Tournament has cancelled');
                } else {
                    return OutputHelper::json(500, array('error_msg' => 'Tournament has cancelled'));
                }
            }

            $ticket_model = & $this->getModel('TournamentTicket', 'TournamentModel');
            $ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

            if (!is_null($ticket)) {
                //return $this->ticketError(JText::_('You already have a ticket for this tournament'), $save, $tournament);
                if ($iframe) {
                    return array('status' => 500, 'error_msg' => 'You already have a ticket for this tournament');
                } else {
                    return OutputHelper::json(500, array('error_msg' => 'You already have a ticket for this tournament'));
                }
            }

            if ($tournament->private_flag == 1) {
                $password = trim(JRequest::getVar('given_password', null));

                $private_tournament_model = & $this->getModel('TournamentPrivate', 'TournamentModel');
                $private_tournament = $private_tournament_model->getTournamentPrivateByTournamentID($id);

                if ($private_tournament->password && $private_tournament->password != $password) {
                    if ($iframe) {
                        return array('status' => 500, 'error_msg' => 'This tournament requries a valid password to enter.');
                    } else {
                        return OutputHelper::json(500, array('error_msg' => 'This tournament requries a valid password to enter.'));
                    }
                }
            }

            $tournament->isRacing = $isRacing;

            if (!empty($tournament->entry_fee) && !empty($tournament->buy_in)) {

                $tb_model = new TopbettaUserModelTopbettaUser();
                if (!$tb_model->isTopbettaUser($user->id)) {
                    if ($iframe) {
                        return array('status' => 500, 'error_msg' => 'You have a basic account. Please upgrade it to enter a paid tournament');
                    } else {
                        return OutputHelper::json(500, array('error_msg' => 'You have a basic account. Please upgrade it to enter a paid tournament'));
                    }
                }

                if (empty($tournament_dollars_model) || empty($tournament_dollars_model)) {
                    //return $this->ticketError(JText::_('You are not allowed to enter tournaments'), $save, $tournament);
                    if ($iframe) {
                        return array('status' => 500, 'error_msg' => 'You are not allowed to enter tournaments');
                    } else {
                        return OutputHelper::json(500, array('error_msg' => 'You are not allowed to enter tournaments'));
                    }
                }

                $tournament_dollars = $tournament_dollars_model->getTotal($user->id);
                $account_balance = $payment_dollars_model->getTotal($user->id);

                $value = $tournament->entry_fee + $tournament->buy_in;
                if ($value > ($tournament_dollars + $account_balance)) {
                    //return $this->ticketError(JText::_('Insufficient funds to purchase the ticket'), $save, $tournament);
                    if ($iframe) {
                        return array('status' => 500, 'error_msg' => 'Insufficient funds to purchase the ticket');
                    } else {
                        return OutputHelper::json(500, array('error_msg' => 'Insufficient funds to purchase the ticket'));
                    }
                }

                //check the account balance spent with bet limit
                $account_balance_spent = $tournament->entry_fee + $tournament->buy_in - $tournament_dollars;
                if ($account_balance_spent > 0 && !$this->_checkBetLimit($account_balance_spent)) {
                    //return $this->ticketError(JText::_('Exceed your bet limit'), $save, $tournament);
                    if ($iframe) {
                        return array('status' => 500, 'error_msg' => 'Exceed your bet limit');
                    } else {
                        return OutputHelper::json(500, array('error_msg' => 'Exceed your bet limit'));
                    }
                }
            }

            //final check - can we save this tournament ticket
            //OutputHelper::_debug($user);
            $ticket_result = $this->storeTicket($tournament, $user, $freeCreditFlag);

            if ($ticket_result['status'] == 200) {
                if ($iframe) {
                    return array('status' => 200, 'success' => $ticket_result['message']);
                } else {
                    return OutputHelper::json(200, array('success' => $ticket_result['message']));
                }
            } else {
                if ($iframe) {
                    return array('status' => 500, 'error_msg' => $ticket_result['message']);
                } else {
                    return OutputHelper::json(500, array('error_msg' => $ticket_result['message']));
                }
            }
        } else {
            if ($iframe) {
                return array('status' => 500, 'error_msg' => 'Invalid Token');
            } else {
                return OutputHelper::json(500, array('error_msg' => 'Invalid Token'));
            }
        }
    }

    /**
     * Validate racing bet selection and save/place bet
     * - Changed to use IGAS wagering provider
     * - NOTE: ONLY WIN and PLACE bets are catered for.
     * - NOTE: Exotics must use the saveRacing Bet function
     */
    public function saveBet()
    {
        global $mainframe;
        // first validate a legit token has been sent
        $server_token = JUtility::getToken();

        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'bet.php');
        $bet_model = new BettingModelBet();

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
            if ((time() - $bet_model->getLastBetTimeStampByUserIDApi($user->id)->created_date) < 2) {
                // $validation->error = JText::_('Please wait a second to make another bet');
                return OutputHelper::json(500, array('error_msg' => 'Please wait a second to make another bet'));
            }
        }

        if ($user->get('guest')) {
            return OutputHelper::json(401, array('error_msg' => 'Not logged in'));
        }

        //Get user status
        require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
        $tb_model = new TopbettaUserModelTopbettaUser();
        if (!$tb_model->isTopbettaUser($user->id)) {
            return OutputHelper::json(500, array('error_msg' => 'You have a basic account. Please upgrade it to place the bet'));
        }

        //JRequest::setVar('id', '1268'); // Tournament ID
        //JRequest::setVar('race_id', '63837'); // Race ID
        //JRequest::setVar('bet_type_id', '3'); // Bet type 1,2 or 3
        //JRequest::setVar('value', '500'); // Bet value
        //JRequest::setVar('selection', '686914'); // Runner ID - runner_list
        //JRequest::setVar('pos', '5'); // Runner position - runner_list
        //JRequest::setVar('bet_origin', 'tournament'); // Bet Racing or Tournament
        //JRequest::setVar('bet_product', '0'); // Bet product Id - runner_list
        //JRequest::setVar('wager_id', '1383248'); // Runner wager ID - runner_list

        $postVars = print_r(JRequest::get('POST'), true);
        file_put_contents('/tmp/igas_racing_betting.log', "POST Vars:" . $postVars . "\n", FILE_APPEND | LOCK_EX);

        //Get the position of the runner
        $pos = JRequest::getVar('pos', '0');

        JRequest::setVar('bet_product', array('first' => array($pos => JRequest::getVar('bet_product', null)))); // Runner wager ID - runner_list
        JRequest::setVar('wager_id', array('first' => array($pos => array(0 => JRequest::getVar('wager_id', null))))); // Runner wager ID - runner_list
        //Get free bet in cents
        $free_bet_amount_input = (float) JRequest::getVar('chkFreeBet', 0);

        if (JRequest::getVar($server_token, FALSE, '', 'alnum')) {

            $validation = new stdClass();
            $validation->relogin = false;
            $validation->error = false;
            $validation->data = array();

            if ($user->guest) {
                $validation->relogin = true;
                $validation->error = JText::_('Please login to place a bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }


            $id = JRequest::getVar('id', null);
            if (is_null($id)) {
                $validation->error = JText::_('No meeting specified');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'meeting.php');
            $meeting_model = new TournamentModelMeeting();
            $meeting = $meeting_model->getMeetingApi($id);

            if (is_null($meeting)) {
                $validation->error = JText::_('Meeting not found');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $meetingID = $meeting->external_event_group_id;
            $meetingType = $meeting->type_code;
            $meetingCountry = $meeting->country;
            $meetingRegion = $meeting->meeting_grade;

            $race_id = JRequest::getVar('race_id', null);
            if (is_null($race_id)) {
                $validation->error = JText::_('No race specified');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'race.php');
            $race_model = new TournamentModelRace();
            $race = $race_model->getRaceApi($race_id);

            $raceNumber = $race->number;

            if (is_null($race)) {
                $validation->error = JText::_('Race was not found');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'eventstatus.php');
            $race_status_model = new TournamentModelEventStatus();
            $selling_status = $race_status_model->getEventStatusByKeywordApi('selling');

            if ($race->event_status_id != $selling_status->id) {
                $validation->error = JText::_('Betting was closed');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }
            
            // special case for greyhounds to allow betting after jump time if allowed only
            // all other race types are always closed via the race status only
            $pastStartCheck = (time() > strtotime($race->start_date)) ? true : false;
            $overRide = $race->override_start;

//            if ($meeting->type_code == "G" && $pastStartCheck && !$overRide) {
            if ($pastStartCheck && !$overRide) {
                $validation->error = JText::_('Betting was closed');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'bet.php');
            $bet_model = new BettingModelBet();

            //if ((time() - $bet_model->getLastBetTimeStampByUserIDApi($user->id)->created_date) < 2) {
            //	$validation->error = JText::_('Please wait a second to make another bet');
            //	return OutputHelper::json(500, array('error_msg' => $validation->error ));
            //}


            $bet_type_id = JRequest::getVar('bet_type_id', null);
            if (is_null($bet_type_id)) {
                $validation->error = JText::_('No bet type selected');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'bettype.php');
            $bet_type_model = new BettingModelBetType();
            $bet_type = $bet_type_model->getBetType($bet_type_id, true);

            if (is_null($bet_type)) {
                $validation->error = JText::_('Invalid bet type selected');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $value = JRequest::getVar('value', null);
            //$value *= 100;
            //MC - app is sending bets in cents - maybe fix this
            //$value = $value * 100;

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'runner.php');
            $runner_model = new TournamentModelRunner();

            //$selection_list = JRequest::getVar('selection', array());
            //$selection_id = JRequest::getVar('selection', NULL);
            //$selection_list = array($pos => (int)$selection_id);

            JRequest::setVar('selection', array('first' => array($pos => JRequest::getVar('selection', null)))); // Runner ID - runner_list

            $selection_list = JRequest::getVar('selection', array());

            //OutputHelper::_debug($selection_list);

            if (empty($selection_list)) {
                $validation->error = JText::_('Invalid bet selections');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $runner_list = $runner_model->getRunnerListByRaceID($race->id);

            $runner_list_by_id = array();
            $runner_list_by_number = array();
            foreach ($runner_list as $runner) {
                $runner_list_by_id[$runner->id] = $runner;
                $runner_list_by_number[$runner->number] = $runner;
            }

            foreach ($selection_list as $selections) {
                foreach ($selections as $selection_id) {
                    if (!isset($runner_list_by_id[$selection_id])) {
                        $validation->error = JText::_('One or more selected runners were not found in this race');
                        return $validation;
                    }
                }
            }


            $boxed_flag = $this->_isBoxedBet($bet_type->name, $selection_list);
            $flexi_flag = $this->_isFlexiBet($bet_type->name, $selection_list);

            $is_exotic_bet_type = $this->_isExoticBetType($bet_type->name);

            $wagering_bet_list = array();
            $bet_total = 0;

            $bet_record = (strtolower($bet_type->name) == 'eachway') ? array('win', 'place') : array($bet_type->name);
            foreach ($bet_record as $type) {
                foreach ($selection_list['first'] as $selection_id) {
                    $bet = WageringBet::newBet($type, $value, false, 0, unserialize($race->external_race_pool_id_list));
                    $bet->addSelection($runner_list_by_id[$selection_id]->number);

                    if (!$bet->isValid()) {
                        $validation->error = JText::_($bet->getErrorMessage());
                        return $validation;
                    } else {
                        $wagering_bet_list[] = $bet;
                        $bet_total += $bet->getTotalBetAmount();
                    }
                }
            }

            $validation->data['wagering_bet_list'] = $wagering_bet_list;

            //For user account
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models' . DS . 'accounttransaction.php');
            $payment_model = new PaymentModelAccounttransaction();
            //For tournament dollars
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models' . DS . 'tournamenttransaction.php');
            $tournamentdollars_model = new TournamentdollarsModelTournamenttransaction();


            //Add free bet amount if exist
            if ($free_bet_amount_input > 0)
                $user_account_total = $payment_model->getTotal($user->id) + $tournamentdollars_model->getTotal($user->id);
            else
                $user_account_total = $payment_model->getTotal($user->id);

            //check user account balance
            if ($bet_total > $user_account_total) {
                $validation->error = JText::_('Insufficient funds to bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            if (!$this->_checkBetLimit($bet_total)) {
                $validation->error = JText::_('Exceed your bet limit');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }


            /* START: HOLD BETS LOCAL
              $api = WageringApi::getInstance(WageringApi::API_IGASRACING);

              $api_con = $api->checkConnection();
              if (is_null($api_con)) {
              $validation->error = JText::_('Service Not Available. Please Try Again Shortly');
              return OutputHelper::json(500, array('error_msg' => $validation->error));
              }
             * END: HOLD BETS LOCAL
             */

            $bet_origin = JRequest::getVar('bet_origin', null);

            if ($bet_origin != 'tournament') {
                $bet_origin = 'betting';
            }


            $validation->data['flexi_flag'] = (int) $flexi_flag;
            $validation->data['meeting'] = $meeting;
            $validation->data['race'] = $race;
            $validation->data['bet_type'] = $bet_type;
            $validation->data['runner_list_by_id'] = $runner_list_by_id;
            $validation->data['runner_list_by_number'] = $runner_list_by_number;
            $validation->data['bet_origin'] = $bet_origin;

            // Bet Validation Ends here
            //http://topbetta.com/api/?method=saveBet&id=1&race_id=3613&bet_type_id=1&selection[]=test&selection[]=testt
            //return OutputHelper::json(200, array('error' => $validation   ));

            $race = isset($validation->data['race']) ? $validation->data['race'] : null;
            $bet_type = isset($validation->data['bet_type']) ? $validation->data['bet_type'] : null;
            $meeting = isset($validation->data['meeting']) ? $validation->data['meeting'] : null;
            $wagering_bet_list = isset($validation->data['wagering_bet_list']) ? $validation->data['wagering_bet_list'] : null;
            $runner_list_by_number = isset($validation->data['runner_list_by_number']) ? $validation->data['runner_list_by_number'] : array();
            $bet_origin_keyword = isset($validation->data['bet_origin']) ? $validation->data['bet_origin'] : 'betting';

            if ($validation->error) {
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // bet_model & bet_type_model are defined earlier
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betselection.php');
            $bet_selection_model = new BettingModelBetSelection();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betresultstatus.php');
            $bet_result_status_model = new BettingModelBetResultStatus();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betproduct.php');
            $bet_product_model = new BettingModelBetProduct();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betorigin.php');
            $bet_origin_model = new BettingModelBetOrigin();

            $failed_status = $bet_result_status_model->getBetResultStatusByNameApi('failed');
            $processing_status = $bet_result_status_model->getBetResultStatusByNameApi('processing');
            $unresult_status = $bet_result_status_model->getBetResultStatusByNameApi('unresulted');
            $refunded_status = $bet_result_status_model->getBetResultStatusByNameApi('fully-refunded');
            $bet_product = $bet_product_model->getBetProductByKeywordApi('supertab-ob');
            $bet_origin = $bet_origin_model->getBetOriginByKeywordApi($bet_origin_keyword);

            // $bet_type_name = $bet_type_model->getBetTypeByName('win', true);
            foreach ($wagering_bet_list as $wagering_bet) {

                $free_bet_amount = ((int) $free_bet_amount_input > 0) ? $tournamentdollars_model->getTotal($user->id) : 0;
                $bet_freebet_transaction_id = $bet_freebet_refund_transaction_id = 0;

                if ($free_bet_amount > 0) {
                    if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                        $bet_freebet_transaction_id = $tournamentdollars_model->decrement($wagering_bet->getTotalBetAmount(), 'freebetentry', null, $user->id); // introducing freebet-entry keyword for transaction type
                    } else {
                        $bet_freebet_transaction_id = $tournamentdollars_model->decrement($free_bet_amount, 'freebetentry', null, $user->id); // introducing freebet-entry keyword for transaction type
                        $bet_transaction_id = $payment_model->decrement(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betentry', null, $user->id);
                    }
                } else
                    $bet_transaction_id = $payment_model->decrement($wagering_bet->getTotalBetAmount(), 'betentry', null, $user->id);

                $bet_type_name = $bet_type_model->getBetTypeByName($wagering_bet->getBetType(), true);
                $bet_product = $bet_product_model->getBetProduct($bet_origin->id);

                $bet = clone $bet_model;

                $bet->external_bet_id = 0;
                $bet->user_id = (int) $user->id;
                $bet->bet_amount = (int) $wagering_bet->getBetAmount();
                $bet->bet_type_id = (int) $bet_type_name->id;
                $bet->bet_result_status_id = (int) $processing_status->id;
                $bet->bet_origin_id = (int) $bet_origin->id;
                $bet->bet_product_id = (int) $bet_product->id;
                $bet->bet_transaction_id = (int) $bet_transaction_id;
                $bet->bet_freebet_transaction_id = (int) $bet_freebet_transaction_id;
                $bet->flexi_flag = (int) $wagering_bet->isFlexiBet() ? 1 : 0;
                $bet->event_id = $race_id;

                // save freebet into the database
                if ($free_bet_amount > 0) {
                    $bet->bet_freebet_flag = 1;
                    if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                        $bet->bet_freebet_amount = (float) $wagering_bet->getTotalBetAmount();
                    } else {
                        $bet->bet_freebet_amount = (float) $free_bet_amount;
                    }
                }
//                 var_dump($bet);
//                 exit;
                $bet_id = $bet->save();

                if (!$bet_id) {

                    if ($free_bet_amount > 0) {
                        // add free bet doallers
                        if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                            $tournamentdollars_model->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund', null, $user->id); // introducing freebetrefund keyword for transaction type
                        } else {
                            $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id); // introducing freebetrefund keyword for transaction type
                            $payment_model->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund', null, $user->id);
                        }
                    } else
                        $payment_model->increment($wagering_bet->getTotalBetAmount(), 'betrefund', null, $user->id);

                    return OutputHelper::json(500, array(
                                'error_msg' => 'Cannot place this bet'
                    ));
                }

                $bet->id = $bet_id;

                $bet_selection_list = $wagering_bet->getBetSelectionList();

                foreach ($bet_selection_list as $pos1 => $bet_selection) {

                    if (!is_array($bet_selection)) {
                        $bet_selection = array(
                            $bet_selection
                        );
                    }

                    foreach ($bet_selection as $runner_number) {

                        $selection = clone $bet_selection_model;

                        $selection->bet_id = (int) $bet_id;
                        $selection->selection_id = (int) $runner_list_by_number [$runner_number]->id;
                        $selection->position = ($wagering_bet->isStandardBetType() || $wagering_bet->isBoxed()) ? 0 : (int) $pos1;
                        if (!$selection->save()) {

                            if ($free_bet_amount > 0) {
                                // add free bet doallers
                                if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                                    $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund', null, $user->id);
                                } else {
                                    $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id);
                                    $bet_refund_transaction_id = $payment_model->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund', null, $user->id);
                                }
                            } else
                                $bet_refund_transaction_id = $payment_model->increment($wagering_bet->getTotalBetAmount(), 'betrefund', null, $user->id);

                            $bet->refund_transaction_id = (int) $bet_refund_transaction_id;
                            $bet->refund_freebet_transaction_id = (int) $bet_freebet_refund_transaction_id;
                            $bet->resulted_flag = 1;
                            $bet->refunded_flag = 1;
                            $bet->bet_result_status_id = (int) $refunded_status->id;
                            $bet->save();
                            return OutputHelper::json(500, array(
                                        'error_msg' => 'Cannot store bet selections'
                            ));
                        }
                    }
                }

                $api_error = null;
                $bet_confirmed = false;

                // TODO: International races need to be catered for. Should configuration or DB driven
                $providerName = "igas";

                if ($bet->bet_type_id == "1") {
                    $betTypeShort = "W";
                } elseif ($bet->bet_type_id == "2") {
                    $betTypeShort = "P";
                }

                // Grab default tote from DB 
                $toteTypeReturn = $bet_product_model->isProductUsed($betTypeShort, $meetingCountry, $meetingRegion, $meetingType, $providerName);
                $toteType = $toteTypeReturn->product_name;


                if ($this->confirmAcceptance($bet_id, $user->id, 'bet', time() + 600)) {

                    $bet_confirmed = true;
                    // we are setting the bet status as unresulted status id: 1
                    $bet->bet_result_status_id = 1;
                    $bet->save();

                    /* START: HOLD BETS LOCAL
                      $external_bet = $api->placeRacingBet($bet->user_id, $bet_id, $bet->bet_amount, $bet->flexi_flag, $meetingID, $raceNumber, $betTypeShort, $toteType, $runner_number);
                      $api_error = $api->getErrorList(true);

                      if (empty($api_error) && isset($external_bet->wagerId)) {
                      $bet_confirmed = true;
                      $bet->external_bet_id = $bet_id; //(int)$external_bet->wagerId;
                      $bet->invoice_id = $external_bet->wagerId;

                      // Set the bet_status based on $external_bet->status
                      $bet_status = 5;
                      if ($external_bet->status == "N" || $external_bet->status == "E") {
                      $bet_status = 5;
                      } elseif ($external_bet->status == "S" || $external_bet->status == "W" || $external_bet->status == "L") {
                      $bet_status = 1;
                      } elseif ($external_bet->status == "F" || $external_bet->status == "CN") {
                      $bet_status = 4;
                      $bet_confirmed = false;
                      }

                      $bet->bet_result_status_id = (int) $bet_status;
                      $bet->save();
                      } else {
                      $bet->external_bet_error_message = (string) $api_error;
                      }
                     * END: HOLD BETS LOCAL
                     */
                }

                if (!$bet_confirmed) {

                    if ($free_bet_amount > 0) {
                        //add free bet dollars
                        if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                            $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund', null, $user->id);
                        } else {
                            $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id);
                            $bet_refund_transaction_id = $payment_model->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund', null, $user->id);
                        }
                    } else
                        $bet_refund_transaction_id = $payment_model->increment($wagering_bet->getTotalBetAmount(), 'betrefund', null, $user->id);

                    $bet->refund_transaction_id = (int) $bet_refund_transaction_id;
                    $bet->refund_freebet_transaction_id = (int) $bet_freebet_refund_transaction_id;
                    $bet->resulted_flag = 1;
                    $bet->refunded_flag = 1;
                    $bet->bet_result_status_id = (int) $failed_status->id;
                    $bet->save();

                    $this->confirmAcceptance($bet_id, $user->id, 'beterror', time() + 600);

                    // Check for TB error code matching
                    require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betErrorCodes.php');
                    $betErrorCodes_model = new BettingModelBetErrorCodes();

                    // pull the error code from the API response
                    preg_match('#\((.*?)\)#', (string) $api_error, $betErrorCode);

                    // If we have a custom error show that - otherwise show the provider error
                    $tbErrorMessage = $betErrorCodes_model->getTBErrorMessage($betErrorCode[1], $providerName);
                    ($tbErrorMessage) ? $errorMessage = $tbErrorMessage->value : $errorMessage = $api_error;

                    return OutputHelper::json(500, array('error_msg' => 'Bet Not Placed: ' . $errorMessage));
                }

                // send our bet off to Risk Manager
                $riskBet = array(
                    'ReferenceId' => $bet->id,
                    'BetDate' => date(DATE_ISO8601),
                    'ClientId' => $user->id,
                    'ClientUsername' => $user->username,
                    'Btag' => $tb_model->getUser($user->id)->btag,
                    'Amount' => $bet->bet_amount,
                    'FreeCredit' => JRequest::getVar('chkFreeBet', 0),
                    'Type' => 'racing',
                    'BetList' => array(
                        'BetType' => $betTypeShort,
                        'PriceType' => ($toteType) ? $toteType : 'TOP',
                        'Selection' => $selection->selection_id,
                        'Position' => $selection->position
                    )
                );

                RiskManagerHelper::sendRacingBet($riskBet);
            }
            return OutputHelper::json(200, array('success' => 'Your bet(s) have been placed'));
        } else {

            return OutputHelper::json(500, array('error_msg' => 'Invalid Token'));
        }
    }

    //TODO: TEMPORARY IGAS BETTING IS BELOW

    /**
     * IGAS - RACE BETTING!
     * - Exotics only
     *
     */
    public function saveRacingBet()
    {
        global $mainframe;
        // first validate a legit token has been sent
        $server_token = JUtility::getToken();

        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
        }
        // $user =& JFactory::getUser();

        if ($user->get('guest')) {
            return OutputHelper::json(401, array('error_msg' => 'Not logged in'));
        }

        //Get user status
        require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
        $tb_model = new TopbettaUserModelTopbettaUser();
        if (!$tb_model->isTopbettaUser($user->id)) {
            return OutputHelper::json(500, array('error_msg' => 'You have a basic account. Please upgrade it to place the bet'));
        }

        //JRequest::setVar('id', '1268'); // Tournament ID
        //JRequest::setVar('race_id', '63837'); // Race ID
        //JRequest::setVar('bet_type_id', '3'); // Bet type 1,2 or 3
        //JRequest::setVar('value', '500'); // Bet value
        //JRequest::setVar('selection', '686914'); // Runner ID - runner_list
        //JRequest::setVar('pos', '5'); // Runner position - runner_list
        //JRequest::setVar('bet_origin', 'tournament'); // Bet Racing or Tournament
        //JRequest::setVar('bet_product', '0'); // Bet product Id - runner_list
        //JRequest::setVar('wager_id', '1383248'); // Runner wager ID - runner_list

        $postVars = print_r(JRequest::get('POST'), true);
        file_put_contents('/tmp/igas_exotics_betting.log', "* Post Vars:" . $postVars . "\n", FILE_APPEND | LOCK_EX);

        //Get free bet in cents
        $free_bet_amount_input = (float) JRequest::getVar('chkFreeBet', 0);

        if (JRequest::getVar($server_token, FALSE, '', 'alnum')) {

            // file_put_contents('/tmp/saveExoticsBet', "* Server Token\n", FILE_APPEND | LOCK_EX);

            $validation = new stdClass();
            $validation->relogin = false;
            $validation->error = false;
            $validation->data = array();

            if ($user->guest) {
                $validation->relogin = true;
                $validation->error = JText::_('Please login to place a bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $id = JRequest::getVar('id', null);
            if (is_null($id)) {
                $validation->error = JText::_('No meeting specified');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'meeting.php');
            $meeting_model = new TournamentModelMeeting();
            $meeting = $meeting_model->getMeetingApi($id);

            if (is_null($meeting)) {
                $validation->error = JText::_('Meeting not found');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $meetingID = $meeting->external_event_group_id;
            $meetingType = $meeting->type_code;

            $race_id = JRequest::getVar('race_id', null);
            if (is_null($race_id)) {
                $validation->error = JText::_('No race specified');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'race.php');
            $race_model = new TournamentModelRace();
            $race = $race_model->getRaceApi($race_id);

            $raceNumber = $race->number;

            if (is_null($race)) {
                $validation->error = JText::_('Race was not found');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'eventstatus.php');
            $race_status_model = new TournamentModelEventStatus();
            $selling_status = $race_status_model->getEventStatusByKeywordApi('selling');

            if ($race->event_status_id != $selling_status->id) {
                $validation->error = JText::_('Betting was closed');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // special case for greyhounds to allow betting after jump time if allowed only
            // all other race types are always closed via the race status only
            $pastStartCheck = (time() > strtotime($race->start_date)) ? true : false;
            $overRide = $race->override_start;

//            if ($meeting->type_code == "G" && $pastStartCheck && !$overRide) {
            if ($pastStartCheck && !$overRide) {
                $validation->error = JText::_('Betting was closed');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'bet.php');
            $bet_model = new BettingModelBet();

            if ((time() - $bet_model->getLastBetTimeStampByUserIDApi($user->id)->created_date) < 2) {
                $validation->error = JText::_('Please wait a second to make another bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $bet_type_id = JRequest::getVar('bet_type_id', null);
            if (is_null($bet_type_id)) {
                $validation->error = JText::_('No bet type selected');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'bettype.php');
            $bet_type_model = new BettingModelBetType();
            $bet_type = $bet_type_model->getBetType($bet_type_id, true);

            if (is_null($bet_type)) {
                $validation->error = JText::_('Invalid bet type selected');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $value = JRequest::getVar('value', null);

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'runner.php');
            $runner_model = new TournamentModelRunner();

            $selection_list = JRequest::getVar('selection', array());

            if (empty($selection_list)) {
                $validation->error = JText::_('Invalid bet selections');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $runner_list = $runner_model->getRunnerListByRaceID($race->id);

            $runner_list_by_id = array();
            $runner_list_by_number = array();
            foreach ($runner_list as $runner) {
                $runner_list_by_id[$runner->id] = $runner;
                $runner_list_by_number[$runner->number] = $runner;
            }

            foreach ($selection_list as $selections) {
                foreach ($selections as $selection_id) {
                    if (!isset($runner_list_by_id[$selection_id])) {
                        $validation->error = JText::_('One or more selected runners were not found in this race');
                        return OutputHelper::json(500, array('error_msg' => $validation->error));
                    }
                }
            }

            $boxed_flag = $this->_isBoxedBet($bet_type->name, $selection_list);
            $flexi_flag = $this->_isFlexiBet($bet_type->name, $selection_list);
            $is_exotic_bet_type = $this->_isExoticBetType($bet_type->name);

            $wagering_bet_list = array();
            $bet_total = 0;

            $bet_record = (strtolower($bet_type->name) == 'eachway') ? array('win', 'place') : array($bet_type->name);
            foreach ($bet_record as $type) {

                if ($is_exotic_bet_type) {

                    $bet = WageringBet::newBet($type, $value, $boxed_flag, $flexi_flag, unserialize($race->external_race_pool_id_list));

                    foreach ($selection_list as $pos => $selections) {

                        $position_number = null;
                        if (!$boxed_flag) {
                            $position_number = $this->getPositionNumber($pos);

                            if (is_null($position_number)) {
                                $validation->error = JText::_('Invalid position number');
                                return OutputHelper::json(500, array('error_msg' => $validation->error));
                            }
                        }

                        foreach ($selections as $selection_id) {
                            $betSelection = $bet->addSelection($runner_list_by_id[$selection_id]->number, $position_number);
                        }
                    }

                    if (!$bet->isValid()) {
                        $validation->error = JText::_($bet->getErrorMessage());
                        return OutputHelper::json(500, array('error_msg' => $validation->error));
                    } else {
                        $wagering_bet_list[] = $bet;
                        $bet_total += $bet->getTotalBetAmount();
                    }
                } else {

                    foreach ($selection_list['first'] as $selection_id) {
                        $bet = WageringBet::newBet($type, $value, false, 0, unserialize($race->external_race_pool_id_list));
                        $bet->addSelection($runner_list_by_id[$selection_id]->number);

                        if (!$bet->isValid()) {
                            $validation->error = JText::_($bet->getErrorMessage());
                            return OutputHelper::json(500, array('error_msg' => $validation->error));
                        } else {
                            $wagering_bet_list[] = $bet;
                            $bet_total += $bet->getTotalBetAmount();
                        }
                    }
                }
            }

            $validation->data['wagering_bet_list'] = $wagering_bet_list;

            //For user account
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models' . DS . 'accounttransaction.php');
            $payment_model = new PaymentModelAccounttransaction();
            //For tournament dollars
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models' . DS . 'tournamenttransaction.php');
            $tournamentdollars_model = new TournamentdollarsModelTournamenttransaction();

            //Add free bet amount if exist
            if ($free_bet_amount_input > 0)
                $user_account_total = $payment_model->getTotal($user->id) + $tournamentdollars_model->getTotal($user->id);
            else
                $user_account_total = $payment_model->getTotal($user->id);

            //check user account balance
            if ($bet_total > $user_account_total) {
                $validation->error = JText::_('Insufficient funds to bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            if (!$this->_checkBetLimit($bet_total)) {
                $validation->error = JText::_('Exceed your bet limit');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            /* START: HOLD BETS LOCAL
              // file_put_contents('/tmp/saveExoticsBet', "* Get API Instance\n", FILE_APPEND | LOCK_EX);
              $api = WageringApi::getInstance(WageringApi::API_IGASEXOTICS);

              $api_con = $api->checkConnection();
              if (is_null($api_con)) {
              $validation->error = JText::_('Service Not Available. Please Try Again Shortly');
              return OutputHelper::json(500, array('error_msg' => $validation->error));
              }
              // file_put_contents('/tmp/saveExoticsBet', "* API Available\n", FILE_APPEND | LOCK_EX);
             * END: HOLD BETS LOCAL
             */

            $bet_origin = JRequest::getVar('bet_origin', null);

            if ($bet_origin != 'tournament') {
                $bet_origin = 'betting';
            }

            $validation->data['flexi_flag'] = (int) $flexi_flag;
            $validation->data['meeting'] = $meeting;
            $validation->data['race'] = $race;
            $validation->data['bet_type'] = $bet_type;
            $validation->data['runner_list_by_id'] = $runner_list_by_id;
            $validation->data['runner_list_by_number'] = $runner_list_by_number;
            $validation->data['bet_origin'] = $bet_origin;

            // file_put_contents('/tmp/saveExoticsBet', "* Validation Complete\n", FILE_APPEND | LOCK_EX);
            // Bet Validation Ends here
            //http://topbetta.com/api/?method=saveBet&id=1&race_id=3613&bet_type_id=1&selection[]=test&selection[]=testt
            //return OutputHelper::json(200, array('error' => $validation   ));

            $race = isset($validation->data['race']) ? $validation->data['race'] : null;
            $bet_type = isset($validation->data['bet_type']) ? $validation->data['bet_type'] : null;
            $meeting = isset($validation->data['meeting']) ? $validation->data['meeting'] : null;
            $wagering_bet_list = isset($validation->data['wagering_bet_list']) ? $validation->data['wagering_bet_list'] : null;
            $runner_list_by_number = isset($validation->data['runner_list_by_number']) ? $validation->data['runner_list_by_number'] : array();
            $bet_origin_keyword = isset($validation->data['bet_origin']) ? $validation->data['bet_origin'] : 'betting';

            if ($validation->error) {
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // bet_model & bet_type_model are defined earlier
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betselection.php');
            $bet_selection_model = new BettingModelBetSelection();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betresultstatus.php');
            $bet_result_status_model = new BettingModelBetResultStatus();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betproduct.php');
            $bet_product_model = new BettingModelBetProduct();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betorigin.php');
            $bet_origin_model = new BettingModelBetOrigin();

            $failed_status = $bet_result_status_model->getBetResultStatusByNameApi('failed');
            $processing_status = $bet_result_status_model->getBetResultStatusByNameApi('processing');
            $unresult_status = $bet_result_status_model->getBetResultStatusByNameApi('unresulted');
            $refunded_status = $bet_result_status_model->getBetResultStatusByNameApi('fully-refunded');
            $bet_product = $bet_product_model->getBetProductByKeywordApi('supertab-ob');
            $bet_origin = $bet_origin_model->getBetOriginByKeywordApi($bet_origin_keyword);

            //$bet_type_name	= $bet_type_model->getBetTypeByName('win', true);

            foreach ($wagering_bet_list as $wagering_bet) {

                // build the bet
                $bet = clone $bet_model;

                // check if its a flexi
                $bet->flexi_flag = (int) $wagering_bet->isFlexiBet() ? 1 : 0;

                // set the flexi percentage
                if ($bet->flexi_flag) {
                    $bet->percentage = $wagering_bet->getFlexiPercentage();
                }

                // if percentage is less than 1 percent then reject the bet
                if ($bet->percentage < 1) {
                    return OutputHelper::json(500, array('error_msg' => 'Bet not placed. Flexi percentage must be greater than 1%'));
                }

                $free_bet_amount = ((int) $free_bet_amount_input > 0) ? $tournamentdollars_model->getTotal($user->id) : 0;
                $bet_freebet_transaction_id = $bet_freebet_refund_transaction_id = 0;

                /*
                 * Deduct the amount from the user's balances that apply
                 */

                if ($free_bet_amount > 0) {
                    if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                        $bet_freebet_transaction_id = $tournamentdollars_model->decrement($wagering_bet->getTotalBetAmount(), 'freebetentry', null, $user->id); // introducing freebet-entry keyword for transaction type
                    } else {
                        $bet_freebet_transaction_id = $tournamentdollars_model->decrement($free_bet_amount, 'freebetentry', null, $user->id); // introducing freebet-entry keyword for transaction type
                        $bet_transaction_id = $payment_model->decrement(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betentry', null, $user->id);
                    }
                } else
                    $bet_transaction_id = $payment_model->decrement($wagering_bet->getTotalBetAmount(), 'betentry', null, $user->id);

                $bet_type_name = $bet_type_model->getBetTypeByName($wagering_bet->getBetType(), true);

                $bet_product = $bet_product_model->getBetProduct($bet_origin->id);

                $exoticCass = 'WageringBetExotic' . $type;



                $bet->external_bet_id = 0;
                $bet->user_id = (int) $user->id;
                $bet->bet_amount = (int) $wagering_bet->getBetAmount();
                $bet->bet_type_id = (int) $bet_type_name->id;
                $bet->bet_result_status_id = (int) $processing_status->id;
                $bet->bet_origin_id = (int) $bet_origin->id;
                $bet->bet_product_id = (int) $bet_product->id;
                $bet->bet_transaction_id = (int) $bet_transaction_id;
                $bet->bet_freebet_transaction_id = (int) $bet_freebet_transaction_id;

                if ($bet->flexi_flag) {
                    $bet->percentage = $wagering_bet->getFlexiPercentage();
                }
                $bet->boxed_flag = (int) $boxed_flag;
                $bet->combinations = $wagering_bet->getCombinationCount();
                $bet->selection_string = $wagering_bet->displayBetSelections();

                //save freebet into the database
                if ($free_bet_amount > 0) {
                    $bet->bet_freebet_flag = 1;
                    if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                        $bet->bet_freebet_amount = (float) $wagering_bet->getTotalBetAmount();
                    } else {
                        $bet->bet_freebet_amount = (float) $free_bet_amount;
                    }
                }

                $bet->event_id = $race_id;

                $bet_id = $bet->save();

                /*
                 * problem saving bet - refund the amounts to accounts that apply
                 */

                // file_put_contents('/tmp/saveExoticsBet', "* TB Bet ID:". $bet_id . "\n", FILE_APPEND | LOCK_EX);
                if (!$bet_id) {

                    if ($free_bet_amount > 0) {
                        //add free bet dollars
                        if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                            $tournamentdollars_model->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund', null, $user->id); // introducing freebetrefund keyword for transaction type
                        } else {
                            $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id); // introducing freebetrefund keyword for transaction type
                            $payment_model->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund', null, $user->id);
                        }
                    } else
                        $payment_model->increment($wagering_bet->getTotalBetAmount(), 'betrefund', null, $user->id);

                    return OutputHelper::json(500, array('error_msg' => 'Cannot place this bet'));
                }

                // file_put_contents('/tmp/saveExoticsBet', "* Total Bet Amount:". $wagering_bet->getTotalBetAmount() . "\n", FILE_APPEND | LOCK_EX);

                $bet->id = $bet_id;

                $bet_selection_list = $wagering_bet->getBetSelectionList();

                foreach ($bet_selection_list as $pos1 => $bet_selection) {

                    if (!is_array($bet_selection)) {
                        $bet_selection = array($bet_selection);
                    }

                    foreach ($bet_selection as $runner_number) {

                        $selection = clone $bet_selection_model;

                        $selection->bet_id = (int) $bet_id;
                        $selection->selection_id = (int) $runner_list_by_number[$runner_number]->id;
                        $selection->position = ($wagering_bet->isStandardBetType() || $wagering_bet->isBoxed()) ? 0 : (int) $pos1;
                        if (!$selection->save()) {

                            if ($free_bet_amount > 0) {
                                //add free bet dollers
                                if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                                    $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund', null, $user->id);
                                } else {
                                    $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id);
                                    $bet_refund_transaction_id = $payment_model->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund', null, $user->id);
                                }
                            } else
                                $bet_refund_transaction_id = $payment_model->increment($wagering_bet->getTotalBetAmount(), 'betrefund', null, $user->id);

                            $bet->refund_transaction_id = (int) $bet_refund_transaction_id;
                            $bet->refund_freebet_transaction_id = (int) $bet_freebet_refund_transaction_id;
                            $bet->resulted_flag = 1;
                            $bet->refunded_flag = 1;
                            $bet->bet_result_status_id = (int) $refunded_status->id;
                            $bet->save();
                            return OutputHelper::json(500, array('error_msg' => 'Cannot store bet selections'));
                        }
                    }
                }

                $api_error = null;
                $bet_confirmed = false;
                // file_put_contents('/tmp/saveExoticsBet', "* About to place bet with IGAS\n", FILE_APPEND | LOCK_EX);
                if ($this->confirmAcceptance($bet_id, $user->id, 'bet', time() + 600)) {
                    $bet_confirmed = true;
                    // we are setting the bet status as unresulted status id: 1
                    $bet->bet_result_status_id = 1;
                    $bet->save();

                    /* START: HOLD BETS LOCAL
                      $external_bet = $api->placeRacingBet($wagering_bet, $meeting, $bet_id, $bet->user_id, $raceNumber, 'SUP', $meetingID);
                      $api_error = $api->getErrorList(true);

                      //$external_bet = 'test123';
                      //$api_error = 'no';

                      if (empty($api_error) && isset($external_bet->wagerId)) {
                      // 	file_put_contents('/tmp/saveExoticsBet', "* Bet Placed\n", FILE_APPEND | LOCK_EX);
                      $bet_confirmed = true;
                      $bet->external_bet_id = $bet_id; //(int)$external_bet->wagerId;
                      $bet->invoice_id = $external_bet->wagerId;

                      // Set the bet_status based on $external_bet->status
                      $bet_status = 5;
                      if ($external_bet->status == "N" || $external_bet->status == "E") {
                      $bet_status = 5;
                      } elseif ($external_bet->status == "S" || $external_bet->status == "W" || $external_bet->status == "L") {
                      $bet_status = 1;
                      } elseif ($external_bet->status == "F" || $external_bet->status == "CN") {
                      $bet_status = 4;
                      $bet_confirmed = false;
                      }


                      $bet->bet_result_status_id = (int) $bet_status;
                      $bet->save();
                      file_put_contents('/tmp/igas_exotics_betting.log', "* Bet Status Saved\n", FILE_APPEND | LOCK_EX);
                      } else {
                      file_put_contents('/tmp/igas_exotics_betting.log', "* Bet NOT Placed\n", FILE_APPEND | LOCK_EX);
                      $bet->external_bet_error_message = (string) $api_error;
                      }
                     * END: HOLD BETS LOCAL
                     */
                }


                if (!$bet_confirmed) {

                    if ($free_bet_amount > 0) {
                        //add free bet dollars
                        if ($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
                            $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund', null, $user->id);
                        } else {
                            $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id);
                            $bet_refund_transaction_id = $payment_model->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund', null, $user->id);
                        }
                    } else
                        $bet_refund_transaction_id = $payment_model->increment($wagering_bet->getTotalBetAmount(), 'betrefund', null, $user->id);

                    $bet->refund_transaction_id = (int) $bet_refund_transaction_id;
                    $bet->refund_freebet_transaction_id = (int) $bet_freebet_refund_transaction_id;
                    $bet->resulted_flag = 1;
                    $bet->refunded_flag = 1;
                    $bet->bet_result_status_id = (int) $failed_status->id;
                    $bet->save();

                    $this->confirmAcceptance($bet_id, $user->id, 'beterror', time() + 600);

                    // Check for TB error code matching
                    require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betErrorCodes.php');
                    $betErrorCodes_model = new BettingModelBetErrorCodes();

                    // pull the error code from the API response
                    preg_match('#\((.*?)\)#', (string) $api_error, $betErrorCode);

                    // If we have a custom error show that - otherwise show the provider error
                    $tbErrorMessage = $betErrorCodes_model->getTBErrorMessage($betErrorCode[1], $providerName);
                    ($tbErrorMessage) ? $errorMessage = $tbErrorMessage->value : $errorMessage = $api_error;

                    return OutputHelper::json(500, array('error_msg' => 'Bet Not Placed: ' . $errorMessage));
                }
                
                // send our bet off to Risk Manager
                $riskBet = array(
                    'ReferenceId' => $bet->id,
                    'EventId' => $bet->event_id,
                    'BetDate' => date(DATE_ISO8601),
                    'ClientId' => $user->id,
                    'ClientUsername' => $user->username,
                    'Btag' => $tb_model->getUser($user->id)->btag,
                    'Amount' => $bet->bet_amount,
                    'FreeCredit' => JRequest::getVar('chkFreeBet', 0),
                    'Type' => 'exotic',
                    'BetList' => array('BetType' => $bet_type_name->id, 'PriceType' => 'TOP'),
                    'FlexiFlag' => $bet->flexi_flag,
                    'BoxedFlag' => $bet->boxed_flag,
                    'Combinations' => $bet->combinations,
                    'Percentage' => $bet->percentage,
                    'SelectionString' => $bet->selection_string,
//                    'SelectionList' => $selection_list
                );

                RiskManagerHelper::sendRacingBet($riskBet);                
                
            }
            return OutputHelper::json(200, array('success' => 'Your bet has been placed'));
        } else {

            return OutputHelper::json(500, array('error_msg' => 'Invalid Token'));
        }
    }

    /**
     * IGAS - SPORTS BETTING!
     *
     *
     */
    public function saveSportBet()
    {
        global $mainframe;
        // first validate a legit token has been sent
        $server_token = JUtility::getToken();

        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
        }
        // $user =& JFactory::getUser();

        if ($user->get('guest')) {
            return OutputHelper::json(401, array('error_msg' => 'Not logged in'));
        }

        // debug file
        $debugflag = 1;
        $file = "/tmp/saveSportsBet";

        //Get user status
        require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
        $tb_model = new TopbettaUserModelTopbettaUser();
        if (!$tb_model->isTopbettaUser($user->id)) {
            return OutputHelper::json(500, array('error_msg' => 'You have a basic account. Please upgrade it to place the bet'));
        }

        // Get sports model
        require_once (JPATH_BASE . DS . 'components' . DS . 'com_sportsbetting' . DS . 'models' . DS . 'sportsbetting.php');
        $sportsBetting_model = new SportsbettingModelSportsbetting();

        $postVars = print_r(JRequest::get('POST'), true);
        file_put_contents('/tmp/saveSportsBet', "* Post Vars:" . $postVars . "\n", FILE_APPEND | LOCK_EX);

        // check if free credit is to be used
        $free_bet_amount_input = (float) JRequest::getVar('chkFreeBet', 0);

        if (JRequest::getVar($server_token, FALSE, '', 'alnum')) {

            $validation = new stdClass();
            $validation->relogin = false;
            $validation->error = false;
            $validation->data = array();

            // check is user is guest/logged in
            if ($user->guest) {
                $validation->relogin = true;
                $validation->error = JText::_('Please login to place a bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $betSelections = JRequest::getVar('bets', null);

            // TODO: not catering for multi bets at this stage.
            foreach ($betSelections as $selection => $betAmount) {
                file_put_contents($file, "* Bet Selection:" . $selection . ". Bet Amount: $betAmount\n", FILE_APPEND | LOCK_EX);
            }



            /*
             * TURN OFF ALL SPORTS BETTING TILL RE-ENABLED
             *
             */
//            $validation->error = JText::_('No sports betting available at this time');
//            return OutputHelper::json(500, array('error_msg' => $validation->error));


            /*
             *  Check all required POST vars are there
             */

            // check that bet amount is greater than 0
            $bet_value = $betAmount;
            if ($bet_value <= 0) {
                $validation->error = JText::_('No bet amount received');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // check if match_id has been passed to the API
            $betMatchID = JRequest::getVar('match_id', null);
            if (is_null($betMatchID)) {
                $validation->error = JText::_('No match ID recieved');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // check if  market has been passed to the API
            $betMarketID = JRequest::getVar('market_id', null);
            if (is_null($betMarketID)) {
                $validation->error = JText::_('No market ID recieved');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // check if bet dividend was passed to the API
            $bet_dividend = JRequest::getVar('dividend', null);
            if (is_null($bet_dividend)) {
                $validation->error = JText::_('No bet dividend received');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // convert dividend to cents
            $bet_dividend = $bet_dividend * 100;

            // get line if passed to API
            $line = JRequest::getVar('line', null);

            if ($line == "0")
                $line = "";

            file_put_contents('/tmp/saveSportsBet', "* MatchID:" . $betMatchID . ". MarketID:$betMarketID, Dividend:$bet_dividend\n", FILE_APPEND | LOCK_EX);


            /*
             *  Check the bet is on valid events, markets and selections
             */

            // check if match_id is in the DB
            $match_exists = $sportsBetting_model->getMatchIDApi($betMatchID);
            if (is_null($match_exists)) {
                $validation->error = JText::_('Match not available');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            $match =  $sportsBetting_model->getEventApi($betMatchID);

            if (strtotime($match->start_date) < time()) {
                return OutputHelper::json(500, array('error_msg' => JText::_('Match has already started')));
            }


//  			// check if market_id is in the DB
//  			$market_exists = $sportsBetting_model->getSelectionIDApi($betMatchID, $bet_option_id);
//  			if (is_null($market_exists)) {
// 				$validation->error = JText::_('Market not available');
// 				return OutputHelper::json(500, array('error_msg' => $validation->error ));
//  			}



            // get the selection/option details
            $selectionDetails = $sportsBetting_model->getSelectionDetailsApi($selection);


            // grab the external id's
            $externalIDs = $sportsBetting_model->getExternalIDsApi($selection);

            // check if external market ID exists for the given selection
            $externalMarketID = $externalIDs->external_market_id;
            if (is_null($externalMarketID)) {
                $validation->error = JText::_('External Market not available');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // check if external selection ID exists for the given selection
            $externalSelectionID = $externalIDs->external_selection_id;
            if (is_null($externalSelectionID)) {
                $validation->error = JText::_('External Selection not available');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // check if external event ID exists for the given selection
            $externalEventID = $externalIDs->external_event_id;
            if (is_null($externalEventID)) {
                $validation->error = JText::_('External event not available');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }



            if ($debugflag == 1) {
                $debug = "- Params passed to API: Free:$free_bet_amount_input, EventID:$externalEventID, Market:$betMarketID, Selection:$selection, BetValue:$bet_value, BetDividend:$bet_dividend\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            // check if betting is still open (check start date on event and is_suspended on price - maybe should be status_id on selection)
            //$nowTime = date("Y-m-d H:i:s");
            //$event_record = $sportsBetting_model->getEventApi($event_id);
            //if ($event_record->start_date < $nowTime ) {
            //	$validation->error = JText::_('Betting was closed');
            //	return OutputHelper::json(500, array('error_msg' => $validation->error ));
            //}
            // check when last bet was made
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'bet.php');
            $bet_model = new BettingModelBet();

            if ((time() - $bet_model->getLastBetTimeStampByUserIDApi($user->id)->created_date) < 2) {
                $validation->error = JText::_('Please wait a second to make another bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            if ($debugflag == 1) {
                $debug = "- Betting open and last bet not within a second\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            //For user account
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models' . DS . 'accounttransaction.php');
            $payment_model = new PaymentModelAccounttransaction();
            //For tournament dollars
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models' . DS . 'tournamenttransaction.php');
            $tournamentdollars_model = new TournamentdollarsModelTournamenttransaction();

            //Add free bet amount if exist
            if ($free_bet_amount_input > 0) {
                $user_account_total = $payment_model->getTotal($user->id) + $tournamentdollars_model->getTotal($user->id);
            } else {
                $user_account_total = $payment_model->getTotal($user->id);
            }

            if ($debugflag == 1) {
                $debug = "- Add Free bet amount if requested\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }
            //check user account balance
            if ($bet_value > $user_account_total) {
                $validation->error = JText::_('Insufficient funds to bet');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            if ($debugflag == 1) {
                $debug = "- Account balance: $user_account_total\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);

                $debug = "- Bet amount: $bet_value\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            if (!$this->_checkBetLimit($bet_value)) {
                $validation->error = JText::_('Exceed your bet limit');
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            if ($debugflag == 1) {
                $debug = "- Account balance and bet limit checked OK\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            /* START: HOLD BETS LOCAL
              // setup the API
              $api = WageringApi::getInstance(WageringApi::API_IGASSPORTS);

              if ($debugflag == 1) {
              $debug = "- Checking API connection\n";
              file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
              }

              // check api is available
              $api_con = $api->checkConnection();

              if (is_null($api_con)) {
              $validation->error = JText::_('Service Not Available. Please Try Again Shortly');
              return OutputHelper::json(500, array('error_msg' => $validation->error));
              }

              if ($debugflag == 1) {
              $debug = "- API connection OK\n";
              file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
              }
             * END: HOLD BETS LOCAL
             */

            $bet_origin = 'sportsbetting';

            $validation->data['bet_origin'] = $bet_origin;
            $bet_origin_keyword = isset($validation->data['bet_origin']) ? $validation->data['bet_origin'] : 'betting';

            if ($validation->error) {
                return OutputHelper::json(500, array('error_msg' => $validation->error));
            }

            // bet_model & bet_type_model are defined earlier
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betselection.php');
            $bet_selection_model = new BettingModelBetSelection();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betresultstatus.php');
            $bet_result_status_model = new BettingModelBetResultStatus();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betproduct.php');
            $bet_product_model = new BettingModelBetProduct();
            require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betorigin.php');
            $bet_origin_model = new BettingModelBetOrigin();

            $failed_status = $bet_result_status_model->getBetResultStatusByNameApi('failed');
            $processing_status = $bet_result_status_model->getBetResultStatusByNameApi('processing');
            $unresult_status = $bet_result_status_model->getBetResultStatusByNameApi('unresulted');
            $refunded_status = $bet_result_status_model->getBetResultStatusByNameApi('fully-refunded');
            $bet_product = $bet_product_model->getBetProductByKeywordApi('supertab-ob');
            $bet_origin = $bet_origin_model->getBetOriginByKeywordApi($bet_origin_keyword);

            if ($debugflag == 1) {
                $debug = "- Bet and Selection models found\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            // deduct from account
            $free_bet_amount = ((int) $free_bet_amount_input > 0) ? $tournamentdollars_model->getTotal($user->id) : 0;
            $bet_freebet_transaction_id = $bet_freebet_refund_transaction_id = 0;

            if ($debugflag == 1) {
                $debug = "- Free Bet amount: $free_bet_amount\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            if ($free_bet_amount > 0) {
                if ($free_bet_amount >= $bet_value) {
                    $bet_freebet_transaction_id = $tournamentdollars_model->decrement($bet_value, 'freebetentry', null, $user->id); // introducing freebet-entry keyword for transaction type
                } else {
                    $bet_freebet_transaction_id = $tournamentdollars_model->decrement($free_bet_amount, 'freebetentry', null, $user->id); // introducing freebet-entry keyword for transaction type
                    $bet_transaction_id = $payment_model->decrement(($bet_value - $free_bet_amount), 'betentry', null, $user->id);
                }
            } else {
                $bet_transaction_id = $payment_model->decrement($bet_value, 'betentry', null, $user->id);
            }

            if ($debugflag == 1) {
                $debug = "- Money taken from user account\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            // $bet_type_name	= $bet_type_model->getBetTypeByName($wagering_bet->getBetType(), true);
            $bet_type_name = "win";

            $bet_product = $bet_product_model->getBetProduct($bet_origin->id);

            $bet = clone $bet_model;

            $bet->external_bet_id = 0;
            $bet->user_id = (int) $user->id;
            $bet->bet_amount = (int) $bet_value;
            $bet->bet_type_id = 1;
            // TODO: Should add other bet types for sport to the __bet_type table
            //$bet->bet_type_id				= (int)$bet_type_name->id;
            $bet->bet_result_status_id = (int) $processing_status->id;
            $bet->bet_origin_id = (int) $bet_origin->id;
            $bet->bet_product_id = (int) $bet_product->id;
            $bet->bet_transaction_id = (int) $bet_transaction_id;
            $bet->bet_freebet_transaction_id = (int) $bet_freebet_transaction_id;
            $bet->flexi_flag = 0;
            $bet->event_id = $betMatchID;
            //$bet->fixed_odds  				= (int)$bet_dividend;
            //save freebet into the database
            if ($free_bet_amount > 0) {
                $bet->bet_freebet_flag = 1;
                if ($free_bet_amount >= $bet_value) {
                    $bet->bet_freebet_amount = (float) $bet_value;
                } else {
                    $bet->bet_freebet_amount = (float) $free_bet_amount;
                }
            }

            if ($debugflag == 1) {
                $debug = "- About to save bet\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            $bet_id = $bet->save();

            if ($debugflag == 1) {
                $debug = "- After save bet\n";
                file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
            }

            // If the bet was not saved then refund it
            if (!$bet_id) {
                if ($debugflag == 1) {
                    $debug = "- Bet Not saved\n";
                    file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
                }
                if ($free_bet_amount > 0) {
                    //add free bet dollars
                    if ($free_bet_amount >= $bet_value) {
                        $tournamentdollars_model->increment($bet_value, 'freebetrefund', null, $user->id); // introducing freebetrefund keyword for transaction type
                    } else {
                        $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id); // introducing freebetrefund keyword for transaction type
                        $payment_model->increment(($bet_value - $free_bet_amount), 'betrefund', null, $user->id);
                    }
                } else {
                    $payment_model->increment($bet_value, 'betrefund', null, $user->id);
                    return OutputHelper::json(500, array('error_msg' => 'Cannot place this bet'));
                }
            }

            $bet->id = $bet_id;

            // grab the id of the selection bet on
            // $selectionID = $sportsBetting_model->getSelectionIDApi($event_id, $bet_option_id);
            $selectionID = $selection;


            // create the selection object
            $selection = clone $bet_selection_model;


            // populate the object data
            $selection->bet_id = (int) $bet_id;
            $selection->selection_id = (int) $selectionID;
            $selection->position = 0;

            // save the bet selction to __bet_selection
            if (!$selection->save()) {
                if ($debugflag == 1) {
                    $debug = "- Failed\n";
                    file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
                }
                if ($free_bet_amount > 0) {
                    //add free bet dollars
                    if ($free_bet_amount >= $bet_value) {
                        $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($bet_value, 'freebetrefund', null, $user->id);
                    } else {
                        $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id);
                        $bet_refund_transaction_id = $payment_model->increment(($bet_value - $free_bet_amount), 'betrefund', null, $user->id);
                    }
                } else
                    $bet_refund_transaction_id = $payment_model->increment($bet_value, 'betrefund', null, $user->id);

                $bet->refund_transaction_id = (int) $bet_refund_transaction_id;
                $bet->refund_freebet_transaction_id = (int) $bet_freebet_refund_transaction_id;
                $bet->resulted_flag = 1;
                $bet->refunded_flag = 1;
                $bet->bet_result_status_id = (int) $refunded_status->id;
                $bet->save();
                return OutputHelper::json(500, array('error_msg' => 'Cannot store bet selections'));
            }

            // placing of the bet via the API
            $api_error = null;
            $bet_confirmed = false;
            if ($this->confirmAcceptance($bet_id, $user->id, 'bet', time() + 600)) {
                $bet_confirmed = true;
                // we are setting the bet status as unresulted status id: 1
                $bet->bet_result_status_id = 1;
                $bet->save();

                /* START: HOLD BETS LOCAL
                  if ($debugflag == 1) {
                  $debug = "- About to send to iGAS API\n";
                  file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
                  }
                  $external_bet = $api->placeSportsBet($bet->user_id, $bet_id, $bet_value, $externalEventID, $externalMarketID, $line, $bet_dividend, $externalSelectionID);
                  $responseArray = print_r($external_bet, true);
                  if ($debugflag == 1) {
                  $debug = "- After bet send to iGAS API, RESPONSE: $responseArray\n";
                  file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
                  }

                  // TODO: Need to get this check working
                  $api_error = $api->getErrorList(true);
                  //$api_error = '';

                  if (empty($api_error) && isset($external_bet->wagerId)) {
                  $bet_confirmed = true;
                  if ($debugflag == 1) {
                  $debug = "- no API error and wagering ID set\n";
                  file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
                  }
                  $bet->external_bet_id = $bet_id; //(int)$external_bet->wagerId;
                  $bet->invoice_id = $external_bet->wagerId;

                  // Set the bet_status based on $external_bet->status
                  $bet_status = 5;
                  if ($external_bet->status == "N" || $external_bet->status == "E") {
                  $bet_status = 5;
                  } elseif ($external_bet->status == "S" || $external_bet->status == "W" || $external_bet->status == "L") {
                  $bet_status = 1;
                  } elseif ($external_bet->status == "F" || $external_bet->status == "CN") {
                  $bet_status = 4;
                  $bet_confirmed = false;
                  }

                  $bet->bet_result_status_id = (int) $bet_status;
                  $bet->save();
                  } else {
                  $bet->external_bet_error_message = (string) $api_error;
                  }
                 * END: HOLD BETS LOCAL
                 */
            }

            // If the bet placement with the API failed
            if (!$bet_confirmed) {
                if ($debugflag == 1) {
                    $debug = "- iGAS API Bet Failed\n";
                    file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
                }
                if ($free_bet_amount > 0) {
                    //add free bet dollars
                    if ($free_bet_amount >= $bet_value) {
                        $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($bet_value, 'freebetrefund', null, $user->id);
                    } else {
                        $bet_freebet_refund_transaction_id = $tournamentdollars_model->increment($free_bet_amount, 'freebetrefund', null, $user->id);
                        $bet_refund_transaction_id = $payment_model->increment(($bet_value - $free_bet_amount), 'betrefund', null, $user->id);
                    }
                } else
                    $bet_refund_transaction_id = $payment_model->increment($bet_value, 'betrefund', null, $user->id);

                $bet->refund_transaction_id = (int) $bet_refund_transaction_id;
                $bet->refund_freebet_transaction_id = (int) $bet_freebet_refund_transaction_id;
                $bet->resulted_flag = 1;
                $bet->refunded_flag = 1;
                $bet->bet_result_status_id = (int) $failed_status->id;
                $bet->save();

                //$betObject = print_r($bet, true);
                //$debug = "- BM API Bet Failed: $betObject\n";
                //file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);

                $this->confirmAcceptance($bet_id, $user->id, 'beterror', time() + 600);

                $validation->error = JText::_('Bet could not be registered');


                // Check for TB error code matching
                require_once (JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' . DS . 'betErrorCodes.php');
                $betErrorCodes_model = new BettingModelBetErrorCodes();

                // pull the error code from the API response
                preg_match('#\((.*?)\)#', (string) $api_error, $betErrorCode);

                // If we have a custom error show that - otherwise show the provider error
                $tbErrorMessage = $betErrorCodes_model->getTBErrorMessage($betErrorCode[1], $providerName);
                ($tbErrorMessage) ? $errorMessage = $tbErrorMessage->value : $errorMessage = $api_error;

                return OutputHelper::json(500, array('error_msg' => 'Bet Not Placed: ' . $errorMessage));




// 					if (isset($external_bet->newOdds)){
// 						return OutputHelper::json(400, array('error_msg' => 'Odds have changed', 'new_odds' => "$external_bet->newOdds" ));
// 					}else{
// 						return OutputHelper::json(500, array('error_msg' => 'Bet could not be registered :' . $api_error ));
// 					}
            }


            // send our bet off to Risk Manager
            $riskBet = array(

                'result_status' => '',
                'dividend' => $bet_dividend, // fixed odds (overall odds for multibets - just same as selection one if only one selection)
                'bet_amount' => $bet->bet_amount - $free_bet_amount, // total real $ amount
                'free_bet_amount' => $free_bet_amount, // totoal FC amount
                'placed_at' => date(DATE_ISO8601), // date bet placed
                'bet_id' => (int) $bet_id, // TB bet ID
                'client_id' => $user->id,
                'client_username' => $user->username,
                'client_btag' => $tb_model->getUser($user->id)->btag,

                'sport_bet_selections' => array(

                    array(
                        // Bet Selection Data - bet_selection record
                        //'bet_selection_id' => '',
                        //'bet_selection_dividend' => '', // is this fixed odds

                        // Option Data - selection record
                        'option_id' => $selection->selection_id,
                        'option_name' => $selectionDetails->option_name,
                        'option_odds' => $bet_dividend,  // is this fixed odds
                        //   'option_line' => '',
                        // 'option_bet_limit' => '', // ?

                        // Market Data - market recors
                        'market_id' => $selectionDetails->market_id,
                        'market_status' => $selectionDetails->market_status,
                        //   'market_line' => '',

                        // Market Type Data
                        'market_type_id' => $selectionDetails->market_type_id,
                        'market_name' => $selectionDetails->market_type_name,

                        // Event Data
                        'event_id' => $selectionDetails->event_id,
                        'event_name' => $selectionDetails->event_name,
                        'event_start_time' => $selectionDetails->event_start_time,

                        // Competition Data
                        'competition_id' => $selectionDetails->competition_id,
                        'competition_name' => $selectionDetails->competition_name,
                        'competition_start_time' => $selectionDetails->competition_start_time,

                        // Sport Data
                        'sport_id' => $selectionDetails->sport_id,
                        'sport_name' => $selectionDetails->sport_name,
                    ),

                ),

            );

            $jsonPayload = json_encode($riskBet);
            file_put_contents('/tmp/riskSportsBet', "RiskPayload: " . $jsonPayload . "\n", FILE_APPEND | LOCK_EX);


            RiskManagerHelper::sendSportBet($riskBet);

            // setup database object rather than use the SUPERMODEL
            $db = & JFactory::getDBO();
            // TODO: Update bet record with correct dividend. Currently it's not stored with the actual bet
            $bet_dividend = $bet_dividend / 100;
            // Update odds and line on bet_selection
            $query = "UPDATE `tbdb_bet_selection` SET `fixed_odds` = '$bet_dividend' WHERE `bet_id` = '$bet_id' AND `selection_id` = '$selectionID'";
            $db->setQuery($query);
            $db->query();
            return OutputHelper::json(200, array('success' => 'Your bet has been placed'));
        } else {
            return OutputHelper::json(500, array('error_msg' => 'Invalid Token'));
        }
    }

    /**
     * Validate a bet selection
     *
     * @param boolean $save
     * @return void
     */
    public function saveTournamentBet($save = true)
    {
        // first validate a legit token has been sent
        $server_token = JUtility::getToken();

        //JRequest::setVar('id', '1268'); // Meeting or Tournament ID
        //JRequest::setVar('race_id', '63837'); // Race ID
        //JRequest::setVar('bet_type_id', '3'); // Bet type 1,2 or 3
        //JRequest::setVar('value', '500'); // Bet value
        //JRequest::setVar('selection', '686914'); // Runner ID - runner_list

        if (JRequest::getVar($server_token, FALSE, '', 'alnum')) {

            // Joomla userid is being passed from Laravel
            // this fixes Joomla forgetting who is logged in :-)
            $l_user_id = JRequest::getVar('l_user_id', NULL);

            if ($l_user_id) {
                $user = & JFactory::getUser($l_user_id);
            } else {
                $user = & JFactory::getUser();
            }
            // $user =& JFactory::getUser();

            $component_list = array('tournament', 'betting');
            foreach ($component_list as $component) {
                $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
                $this->addModelPath($path);
            }

            // begin the painstaking task of validating a bet
            $id = JRequest::getVar('id', null);
            if (is_null($id)) {
                return OutputHelper::json(500, array('error_msg' => 'No tournament specified'));
            }

            $tournament_model = & $this->getModel('TournamentRacing', 'TournamentModel');
            $tournament = $tournament_model->getTournamentRacingByTournamentID($id);

            if (is_null($tournament)) {
                return OutputHelper::json(500, array('error_msg' => 'Tournament not found'), $save);
            }

            if($tournament->closed_betting_on_first_match_flag){
                if (strtotime($tournament->betting_closed_date) < time()) {
                        return OutputHelper::json(500, array('error_msg' => 'Betting is already closed.'));
                }
            }

            $ticket_model = & $this->getModel('TournamentTicket', 'TournamentModel');
            $ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

            if (is_null($ticket)) {
                return OutputHelper::json(500, array('error_msg' => 'You do not have a ticket for the selected tournament'));
            }

            $race_id = JRequest::getVar('race_id', null);
            if (is_null($race_id)) {
                return OutputHelper::json(500, array('error_msg' => 'No race specified'));
            }

            $race_model = & $this->getModel('Race', 'TournamentModel');
            $race = $race_model->getRace($race_id);

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' . DS . 'eventstatus.php');
            $race_status_model = new TournamentModelEventStatus();

            $selling_status = $race_status_model->getEventStatusByKeywordApi('selling');

            if (is_null($race)) {
                return OutputHelper::json(500, array('error_msg' => 'Race was not found'));
            }

            if ($race->event_status_id != $selling_status->id) {
                return OutputHelper::json(500, array('error_msg' => 'Betting was closed'));
            }

            // Check to see if the race is past the start time as well as if the override flag is not true. This will allow
            // bets to be placed after start time when applicable. NOTE: This logic has been replicated from the `sveBet`
            // method
            $pastStartCheck = (time() > strtotime($race->start_date)) ? true : false;
            $overRide = $race->override_start;

//            if (strtotime($race->start_date) < time()) {
            if ($pastStartCheck && !$overRide) {
                return OutputHelper::json(500, array('error_msg' => 'Race has already jumped'));
            }

            $bet_type_id = JRequest::getVar('bet_type_id', null);
            if (is_null($bet_type_id)) {
                return OutputHelper::json(500, array('error_msg' => 'No bet type selected'));
            }

            $bet_type_model = & $this->getModel('BetType', 'BettingModel');
            $bet_type = $bet_type_model->getBetType($bet_type_id, true);

            if (is_null($bet_type)) {
                return OutputHelper::json(500, array('error_msg' => 'Invalid bet type selected'));
            } else if (!WageringBet::isStandardBetType($bet_type->name)) {
                return OutputHelper::json(500, array('error_msg' => 'Exotic bets are not currently supported for tournaments. Coming soon!'));
            }

            $value = JRequest::getVar('value', null);
            if (empty($value)) { // using empty to account for 0 as well
                return OutputHelper::json(500, array('error_msg' => 'No bet value specified'));
            }

            $selection = JRequest::getVar('selection', null);
            if (empty($selection)) {
                return OutputHelper::json(500, array('error_msg' => 'Invalid bet selections'));
            }

            $selection_list = explode(',', $selection);
            if (count($selection_list) == 0) {
                return OutputHelper::json(500, array('error_msg' => 'No selections found'));
            }

            $runner_model = & $this->getModel('Runner', 'TournamentModel');
            $runner_list = $runner_model->getRunnerListByRaceID($race->id);

            $runner_validation_list = array();
            foreach ($runner_list as $runner) {
                $runner_validation_list[$runner->id] = $runner;
            }

            $selected_runner_list = array();
            foreach ($selection_list as $selection_id) {
                if (isset($runner_validation_list[$selection_id])) {
                    $selected_runner_list[] = $runner_validation_list[$selection_id];
                    continue;
                }

                return OutputHelper::json(500, array('error_msg' => 'One or more selected runners were not found in this race'));
            }

            //$value *= 100;
            $bet_total = count($selected_runner_list) * $value;
            if (strtolower($bet_type->name) == 'eachway') {
                $bet_total *= 2;
            }

            $current_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);

            if ($current_currency < $bet_total) {
                $required = number_format(($bet_total - $current_currency) / 100, 2);
                return OutputHelper::json(500, array('error_msg' => 'You do not have enough bucks to place that bet (' . $required . ' more needed)'));
            }

            if (!$tournament->reinvest_winnings_flag) {
                $leaderboard_model = & $this->getModel('TournamentLeaderboard', 'TournamentModel');
                $turnover = $leaderboard_model->getTurnedOverByUserAndTournamentID($user->id, $tournament->id);

                if ($turnover + $bet_total > $tournament->start_currency) {
                    $maximum_total_bet = number_format($tournament->start_currency / 100, 2);
                    return OutputHelper::json(500, array('error_msg' => JText::_('Your total bets cannot be more than ' . $maximum_total_bet)));
                }
            }

            // validation complete, so save or display depending on $save value
            if ($save) {
                $this->storeBet($ticket, $race, $bet_type, $value, $selected_runner_list);
                return OutputHelper::json(200, array('success' => 'Your bet has been placed'));
            }
        } else {

            return OutputHelper::json(500, array('error_msg' => 'Invalid Token'));
        }
    }

    /**
     * Store a bet selection record
     *
     * @param object  $ticket
     * @param object  $race
     * @param object  $bet_type
     * @param integer $value
     * @param array   $selected_runner_list
     * @return void
     */
    private function storeBet($ticket, $race, $bet_type, $value, $selected_runner_list)
    {
        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
        }
        // $user =& JFactory::getUser();
        $component_list = array('tournament');
        foreach ($component_list as $component) {
            $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
            $this->addModelPath($path);
        }

        $bet_model = & $this->getModel('TournamentBet', 'TournamentModel');
        $bet_selection_model = & $this->getModel('TournamentBetSelection', 'TournamentModel');

        $bet_record = ($bet_type->name == 'eachway') ? array('win', 'place') : array($bet_type->name);

        $bet_total = 0;
        $error = false;

        foreach ($selected_runner_list as $runner) {
            foreach ($bet_record as $type) {
                $bet = array(
                    'tournament_ticket_id' => $ticket->id,
                    'bet_result_status' => 'unresulted',
                    'bet_type' => $type,
                    'bet_amount' => $value
                );

                $bet_id = $bet_model->storeUsingTypeNames($bet);

                $bet_total += $value;

                $bet_selection = array(
                    'tournament_bet_id' => (int) $bet_id,
                    'selection_id' => (int) $runner->id,
                    'position' => 0
                );
                $bet_selection_model->store($bet_selection);

                if (!$this->confirmAcceptance($bet_id, $user->id, 'tournamentbet', strtotime($race->start_date))) {
                    $error = true;

                    $bet['id'] = $bet_id;
                    $bet['resulted_flag'] = 1;
                    $bet['win_amount'] = $value;
                    $bet['bet_result_status'] = 'fully-refunded';

                    $bet_model->storeUsingTypeNames($bet);
                    $bet_total -= $value;
                }
            }
        }

        if ($bet_total > 0) {
            $leaderboard_model = & $this->getModel('TournamentLeaderboard', 'TournamentModel');
            $leaderboard_model->addTurnedOverByUserAndTournamentID($user->id, $ticket->tournament_id, $bet_total);
        }

        if ($error) {
            return OutputHelper::json(500, array('error_msg' => 'One or more bets could not be saved'));
        } else {
            return OutputHelper::json(200, array('success' => 'Bets have been registered'));
        }
    }

    /**
     * Validate and save a bet tournament sports bet selection
     *
     * @return void
     */
    public function saveTournamentSportsBet()
    {
        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
        }
        // $user =& JFactory::getUser();
        // begin the painstaking task of validating a bet
        $id = JRequest::getVar('id', null);
        $match_id = JRequest::getVar('match_id', null);
        $market_id = JRequest::getVar('market_id', null);
        $bets = JRequest::getVar('bets', array());

        //$session				=& JFactory::getSession();
        //$pending_bet_list		= $session->get('pending_bet_list', array(), 'sports_tournaments');
        //$market_timestamp_list	= $session->get('market_timestamp_list', array(), 'sports_tournaments');
        //$bet_ticket_timestamp	= $session->get('bet_ticket_timestamp', array(), 'sports_tournaments');

        if (is_null($id)) {
            return OutputHelper::json(500, array('error_msg' => JText::_('No tournament specified')));
        }

        if (is_null($match_id)) {
            return OutputHelper::json(500, array('error_msg' => JText::_('No match specified')));
        }

        $component_list = array('betting', 'tournament', 'tournament_dollars', 'topbetta_user', 'payment');
        foreach ($component_list as $component) {
            $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
            $this->addModelPath($path);
        }

        $tournament_model = & $this->getModel('TournamentSportEvent', 'TournamentModel');
        $tournament = $tournament_model->getTournamentSportsByTournamentID($id);

        if (is_null($tournament)) {
            return OutputHelper::json(500, array('error_msg' => JText::_('Tournament not found')));
        }

        $ticket_model = & $this->getModel('TournamentTicket', 'TournamentModel');
        $ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

        if (is_null($ticket)) {
            return OutputHelper::json(500, array('error_msg' => JText::_('You do not have a ticket for the selected tournament')));
        }

        $match_model = & $this->getModel('Event', 'TournamentModel');
        $match = $match_model->getEvent($match_id);
        $match->tournament_match_id = $match->id;

        if (empty($match)) {
            return OutputHelper::json(500, array('error_msg' => JText::_('Match was not found')));
        }

        if (strtotime($match->start_date) < time()) {
            return OutputHelper::json(500, array('error_msg' => JText::_('Match has already started')));
        }

        if (strtotime($tournament->betting_closed_date) < time()) {
            return OutputHelper::json(500, array('error_msg' => JText::_('Betting is already closed')));
        }

        $market_model = & $this->getModel('Market', 'TournamentModel');

        $pending_bet_list[$id][$market_id] = array();
        foreach ($bets as $offer_id => $value) {
            $pending_bet_list[$id][$market_id][$offer_id] = $value;
        }

        //TODO: not sure if this is still needed
        $market_timestamp_list[$market_id] = time();
        $bet_ticket_timestamp[$id] = time();

        /*
          if(!$save) {
          if(is_null($market_id)) {
          return OutputHelper::json(500, array('error_msg' => JText::_('No market specified')));
          }

          $market_list	= $market_model->getMarketListByEventIDAndEventGroupID($match->id, $tournament->event_group_id);

          if(!isset($market_list[$market_id])) {
          return OutputHelper::json(500, array('error_msg' => JText::_('Market was not found')));
          }
          $market = $market_model->getMarket($market_id);

          $pending_bet_list[$id][$market_id] = array();
          foreach($bets as $offer_id => $value) {
          $pending_bet_list[$id][$market_id][$offer_id] = $value;
          }
          $session->set('pending_bet_list', $pending_bet_list, 'sports_tournaments');

          if(empty($pending_bet_list)) {
          return OutputHelper::json(500, array('error_msg' => JText::_('No offers specified')));
          }
          }
         */

        $selection_model = & $this->getModel('Selection', 'TournamentModel');
        $bet_model = & $this->getModel('TournamentBet', 'TournamentModel');
        $bet_list = array();
        $bet_total = 0;

        $offer_updated_list = array();
        $odds_updated = false;

        if (isset($pending_bet_list[$tournament->id]) && is_array($pending_bet_list[$tournament->id])) {
            foreach ($pending_bet_list[$tournament->id] as $tournament_market_id => $market_offers) {
                $updated_list = $selection_model->getUpdatedSelectionListByMarketID($tournament_market_id, $market_timestamp_list[$tournament_market_id]);
                $market = $market_model->getMarket($tournament_market_id);

                if (empty($updated_list) && isset($bet_ticket_timestamp[$tournament->id])) {
                    $updated_list = $selection_model->getUpdatedSelectionListByMarketID($tournament_market_id, $bet_ticket_timestamp[$tournament->id]);
                }

                if (!empty($updated_list)) {
                    $offer_updated_list[$tournament_market_id] = $updated_list;
                    $odds_updated = true;
                }

                if ($tournament->bet_limit_flag) {
                    $offer_count = count($selection_model->getSelectionListByMarketID($tournament_market_id));
                    if (isset($market_model->offer_market_limit[$offer_count])) {
                        $market_bet_limit = $market_model->offer_market_limit[$offer_count];
                    } else {
                        $market_bet_limit = $market_model->offer_market_limit[9];
                    }

                    if ('unlimited' == $market_bet_limit) {
                        $market_bet_limit = $tournament->start_currency;
                    }
                }

                foreach ($market_offers as $offer_id => $value) {
                    $offer = $selection_model->getSelectionDetails($offer_id);
                    $pending_offer_bet_value = 0;

                    if ($offer->market_id == $tournament_market_id && $value > 0) {
                        $bet_value = $value;
                        $bet_list[$offer_id] = $bet_value;
                        $bet_total += $bet_value;
                        $pending_offer_bet_value += $bet_value;
                    }

                    if ($tournament->bet_limit_flag) {
                        $offer_betted_value = $bet_model->getTournamentBetTotalsBySelectionIDAndTicketID($offer_id, $ticket->id);

                        $offer_bet_value_credit = $market_bet_limit - $offer_betted_value;

                        if ($offer_bet_value_credit < $pending_offer_bet_value) {
                            $maximum_bet = number_format($offer_bet_value_credit, 2);
                            return OutputHelper::json(500, array('error_msg' => JText::_('Your bet for ' . $offer->name . ' (' . $offer->market_type . ') has exceeded the bet limit. You can only bet ' . $maximum_bet)));
                        }
                    }
                }
            }
        }

        if (empty($bet_list)) {
            return OutputHelper::json(500, array('error_msg' => JText::_('Please enter at least a bet.')));
        }
        //odds has been updated, refresh the bet form
        if ($odds_updated) {
            return OutputHelper::json(500, array('error_msg' => JText::_('Odds have updated.')));
            //$this->betRefresh();
            //return;
        }

        $current_currency = $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);

        if ($current_currency < $bet_total) {
            $required = number_format(($bet_total - $current_currency) / 100, 2);
            return OutputHelper::json(500, array('error_msg' => JText::_('You do not have enough bucks to place that bet (' . $required . ' more needed)')));
        }

        if (!$tournament->reinvest_winnings_flag) {
            $leaderboard_model = & $this->getModel('TournamentLeaderboard', 'TournamentModel');
            $turnover = $leaderboard_model->getTurnedOverByUserAndTournamentID($user->id, $tournament->id);

            if ($turnover + $bet_total > $tournament->start_currency) {
                $maximum_total_bet = number_format($tournament->start_currency / 100, 2);
                return OutputHelper::json(500, array('error_msg' => JText::_('Your total bets cannot be more than ' . $maximum_total_bet)));
            }
        }

        // validation complete, so save bet
        $error = $this->storeTournamentSportsBet($tournament, $ticket, $match, $bet_list);

        if ($error) {
            return OutputHelper::json(500, array('error_msg' => JText::_('One or more bets could not be saved')));
        } else {
            return OutputHelper::json(200, array('success' => 'Bets have been registered'));
        }
    }

    /**
     * Store a bet selection record
     *
     * @param object  $tournament
     * @param object  $ticket
     * @param object  $match
     * @param array   $bet_list
     * @return void
     */
    private function storeTournamentSportsBet($tournament, $ticket, $match, $bet_list)
    {
        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
        }
        // $user 					=& JFactory::getUser();
        $bet_model = & $this->getModel('TournamentBet', 'TournamentModel');
        $bet_selection_model = & $this->getModel('TournamentBetSelection', 'TournamentModel');
        $bet_status_model = & $this->getModel('BetResultStatus', 'BettingModel');
        $offer_model = & $this->getModel('Selection', 'TournamentModel');
        $bet_product_model = & $this->getModel('BetProduct', 'BettingModel');
        $bet_type_model = & $this->getModel('BetType', 'BettingModel');
        $bet_total = 0;
        $error = false;

        $unitab = $bet_product_model->getBetProductByKeyword('unitab');
        $win_bet_type = $bet_type_model->getBetTypeByName('win');
        $bet_status_unresulted = $bet_status_model->getBetResultStatusByName('unresulted');

        foreach ($bet_list as $offer_id => $bet_value) {

            $offer = $offer_model->getSelectionDetails($offer_id);
            $odds = $offer->win_odds;
            if (!empty($offer->override_odds) && $offer->override_odds < $offer->win_odds) {
                $odds = $offer->override_odds;
            }

            $bet = array(
                'id' => null,
                'tournament_ticket_id' => $ticket->id,
                'bet_result_status_id' => $bet_status_unresulted->id,
                'bet_type_id' => $win_bet_type->id,
                'bet_product_id' => $unitab->id,
                'bet_amount' => $bet_value,
                'win_amount' => 0,
                'fixed_odds' => $odds,
                'flexi_flag' => 0,
                'resulted_flag' => 0,
            );

            $id = $bet_model->store($bet);
            $bet_total += $bet_value;

            $bet_selection = array(
                'id' => null,
                'selection_id' => $offer->id,
                'tournament_bet_id' => $id,
                'position' => null
            );
            $bet_selection_model->store($bet_selection);

            $betting_closed_date = $match->start_date;
            if (!empty($tournament->betting_closed_date) && ($match->start_date > $tournament->betting_closed_date)) {
                $betting_closed_date = $tournament->betting_closed_date;
            }

            //TODO: do we really need to send tournament bets to NI?
            /*
              if(!$this->confirmAcceptance($id, $user->id, 'tournamentsportbet', strtotime($betting_closed_date))) {
              $error					= true;
              $bet_status_refunded	= $bet_status_model->getBetResultStatusByName('fully-refunded');

              $bet['id']					= $id;
              $bet['resulted_flag']		= 1;
              $bet['win_amount']			= $bet_value;
              $bet['bet_result_status_id']	= $bet_status_refunded->id;

              $bet_model->store($bet);
              $bet_total -= $bet_value;
              }
             */
        }

        if ($bet_total > 0) {
            $leaderboard_model = & $this->getModel('TournamentLeaderboard', 'TournamentModel');
            $leaderboard_model->addTurnedOverByUserAndTournamentID($user->id, $ticket->tournament_id, $bet_total);
        }

        return $error;
    }

    /**
     * Store a ticket purchase in the database
     *
     * @param object $tournament
     * @param object $user
     * @return void
     */
    protected function storeTicket($tournament, $user, $freeCreditFlag)
    {
        //var_dump($user);
        //exit;

        $component_list = array('betting', 'tournament', 'tournament_dollars', 'topbetta_user', 'payment');
        foreach ($component_list as $component) {
            $path = JPATH_SITE . DS . 'components' . DS . 'com_' . $component . DS . 'models';
            $this->addModelPath($path);
        }

        $ticket_model = & $this->getModel('TournamentTicket', 'TournamentModel');

        if (!class_exists('TournamentdollarsModelTournamenttransaction')) {
            JLoader::import('tournamenttransaction', JPATH_BASE . DS . 'components' . DS . 'com_tournamentdollars' . DS . 'models');
        }
        $tournament_dollars_model = JModel::getInstance('Tournamenttransaction', 'TournamentdollarsModel');

        if (!class_exists('PaymentModelAccounttransaction')) {
            JLoader::import('accounttransaction', JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models');
        }
        $payment_dollars_model = JModel::getInstance('Accounttransaction', 'PaymentModel');

        //file_put_contents('/tmp/igas_tourn_ticket.log', "FreeCreditFlag:$freeCreditFlag\n", FILE_APPEND | LOCK_EX);

        if (!$freeCreditFlag) {
            // grab ticket cost and account balance.
            $totalTicketCost = $tournament->buy_in + $tournament->entry_fee;
            $userAccountBalance = $payment_dollars_model->getTotal($user->id);

            //transfer total cost
            if ($userAccountBalance >= $totalTicketCost) {
                // remove money from account balance
                $payment_dollars_model->decrement($tournament->entry_fee, 'entry', null, $user->id);
                $payment_dollars_model->decrement($tournament->buy_in, 'buyin', null, $user->id);

                //put money in free credit
                $tournament_dollars_model->increment($tournament->entry_fee, 'purchase', 'Transferred from account balance', $user->id);
                $tournament_dollars_model->increment($tournament->buy_in, 'purchase', 'Transferred from account balance', $user->id);
            } else {// transfer whats there
                // remove money from account balance
                //$payment_dollars_model->decrement($tournament->entry_fee, 'entry', null, $user_id);
                $payment_dollars_model->decrement($userAccountBalance, 'buyin', null, $user->id);

                //put money in free credit
                //$tournament_dollars_model->increment($tournament->entry_fee, 'purchase', 'Transferred from account balance', $user_id);
                $tournament_dollars_model->increment($userAccountBalance, 'purchase', 'Transferred from account balance', $user->id);
            }
        }

        // pay for the ticket with free credit if possible
        $buy_in_id = $tournament_dollars_model->decrement($tournament->buy_in, 'buyin', null, $user->id);
        $entry_fee_id = $tournament_dollars_model->decrement($tournament->entry_fee, 'entry', null, $user->id);

        $ticket = array(
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
            'entry_fee_transaction_id' => $entry_fee_id,
            'buy_in_transaction_id' => $buy_in_id,
            'refunded_flag' => 0,
            'resulted_flag' => 0
        );

        $ticket_id = $ticket_model->store($ticket);
        $betting_closed_date = ($tournament->betting_closed_date ? $tournament->betting_closed_date : $tournament->end_date);
        $ticket_result = array();
        if ($this->confirmAcceptance($ticket_id, $user->id, 'tournamentticket', strtotime($betting_closed_date))) {
            $leaderboard_model = & $this->getModel('TournamentLeaderboard', 'TournamentModel');
            $leaderboard = array(
                'user_id' => $user->id,
                'tournament_id' => $tournament->id,
                'currency' => $tournament->start_currency
            );

            $leaderboard_model->store($leaderboard);

            $sport_model = & $this->getModel('TournamentSport', 'TournamentModel');
            $is_racing_tournament = $sport_model->isRacingByTournamentId($tournament->id);
            $tournament_type = $is_racing_tournament ? 'racing' : 'sports';

            //$url = '/tournament/'.$tournament_type.'/game/' . $tournament->id;
            $ticket_result['message'] = JText::_('Ticket purchase confirmed');
            $ticket_result['status'] = 200;
            //$type = 'message';

            /*
              if($user->entriesToFbWall == '1'){

              require_once JPATH_ROOT.DS.'components'.DS.'com_jfbconnect'.DS.'libraries'.DS.'facebook.php';
              $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();

              $post['caption'] = 'TopBetta Tournament Entry';
              $post['message'] = 'has entered a tournament on TopBetta.com';
              $post['link'] = 'https://www.topbetta.com'.$url;
              $post['picture'] = 'https://www.topbetta.com/images/topbetta-logo.jpg';

              if ($jfbcLibrary->getUserId()){ // Check if there is a Facebook user logged in

              $jfbcLibrary->setFacebookMessageWall($post,$user->id);
              //$jfbcLibrary->setFacebookMessage($post);

              }
              }
             */
        } else {
            $ticket = $ticket_model->getTournamentTicket($ticket_id);
            $refund_id = $user->tournament_dollars->increment($tournament->buy_in + $tournament->entry_fee, 'refund', null, $user->id);

            $ticket->refunded_flag = 1;
            $ticket->result_transaction_id = $refund_id;

            $ticket_model->store((array) $ticket);

            //$url = '/tournament/details/' . $tournament->id;
            //$message = JText::_('Ticket could not be purchased');
            $ticket_result['message'] = JText::_('Ticket could not be purchased');
            $ticket_result['status'] = 500;
            //$type = 'error';
        }
        //$this->setRedirect($url, $message, $type);
        return $ticket_result;
    }

    /**
     * Call the processing server for acceptance if required
     *
     * @param integer $transaction_id
     * @param integer $user_id
     * @param string  $type
     * @param integer $deadline
     * @return boolean
     */
    protected function confirmAcceptance($transaction_id, $user_id, $type, $deadline)
    {
        $config = & JFactory::getConfig();
        $enabled = $config->getValue('config.remote_processing');

        if ($enabled) {
            $processing = array(
                'method' => 'confirm_acceptance',
                'transaction_id' => $transaction_id,
                'user_id' => $user_id,
                'type' => $type,
                'initiated_date' => time(),
                'deadline_date' => $deadline
            );

            $response = TournamentHelper::callProcessingServer($processing);
            return (!empty($response) && $response->status == 'accepted');
        }

        return true;
    }

    /**
     * Check Bet limit
     *
     * @return boolean
     */
    protected function _checkBetLimit($bet_total)
    {
        // Joomla userid is being passed from Laravel
        // this fixes Joomla forgetting who is logged in :-)
        $l_user_id = JRequest::getVar('l_user_id', NULL);

        if ($l_user_id) {
            $user = & JFactory::getUser($l_user_id);
        } else {
            $user = & JFactory::getUser();
        }

        // $user =& JFactory::getUser();
        require_once (JPATH_BASE . DS . 'components' . DS . 'com_topbetta_user' . DS . 'models' . DS . 'topbettauser.php');
        $user_model = new TopbettaUserModelTopbettaUser();
        $user_data = $user_model->getUser($user->id);


        if ($user_data->bet_limit != -1) {
            $from_time = strtotime(date('Y-m-d'));

            require_once (JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models' . DS . 'accounttransaction.php');
            $payment_model = new PaymentModelAccounttransaction();

            $today_betting = $payment_model->getTotalAmountByTransactionType('betentry', $user->id, $from_time);
            $today_winning = $payment_model->getTotalAmountByTransactionType('betwin', $user->id, $from_time);
            $today_refund = $payment_model->getTotalAmountByTransactionType('betrefund', $user->id, $from_time);

            $today_tournament_entry = $payment_model->getTotalAmountByTransactionType('entry', $user->id, $from_time);
            $today_tournament_buyin = $payment_model->getTotalAmountByTransactionType('buyin', $user->id, $from_time);
            $today_tournament_win = $payment_model->getTotalAmountByTransactionType('tournamentwin', $user->id, $from_time);

            $total_winning = $today_winning + $today_refund + $today_tournament_win;
            $total_spending = abs($today_betting + $today_tournament_entry + $today_tournament_buyin);

            if (($user_data->bet_limit + $total_winning - $bet_total - $total_spending) < 0) {
                return false;
            }
        }
        return true;
    }

    private function _isExoticBetType($bet_type_name)
    {
        if (in_array($bet_type_name, WageringBet::$standard_bet_type_list)) {
            return false;
        }
        return true;
    }

    private function _isBoxedBet($bet_type_name, $selection_list)
    {

        if (!$this->_isExoticBetType($bet_type_name)) {
            return false;
        }

        unset($selection_list['first']);
        return count($selection_list) == 0;
    }

    private function _isFlexiBet($bet_type_name)
    {

        //if ($bet_type_name != WageringBet::BET_TYPE_TRIFECTA && $bet_type_name != WageringBet::BET_TYPE_FIRSTFOUR) {
        //	return false;
        //}

        return JRequest::getVar('flexi', false);
    }

    /**
     * Format the display of a countdown to a specified time
     *
     * @param integer $time
     * @return string
     */
    protected function formatCounterText($time)
    {
        if ($time < time()) {
            return FALSE;
        }

        $remaining = $time - time();

        $days = intval($remaining / 3600 / 24);
        $hours = intval(($remaining / 3600) % 24);
        $minutes = intval(($remaining / 60) % 60);
        $seconds = intval($remaining % 60);

        $text = $seconds . ' sec';
        if ($minutes > 0) {
            $text = $minutes . ' min';
        }

        if ($hours > 0) {
            $min_sec_text = '';

            if ($days == 0) {
                $min_sec_text = $text;
            }

            $text = $hours . ' hr ' . $min_sec_text;
        }

        if ($days > 0) {
            $text = $days . ' d ' . $text;
        }
        return $text;
    }

    public static function getPositionNumber($position)
    {
        $position_number = null;
        switch ($position) {
            case 'first':
                $position_number = 1;
                break;
            case 'second':
                $position_number = 2;
                break;
            case 'third':
                $position_number = 3;
                break;
            case 'fourth':
                $position_number = 4;
                break;
        }

        return $position_number;
    }

}

?>