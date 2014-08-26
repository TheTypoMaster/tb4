<?php
/**
* @version		$Id: poll.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla
* @subpackage	Polls
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


/**
* @package		Joomla
* @subpackage	Polls
*/
class SportsbettingModelSportsbetting extends JModel {

	function __construct() {
		parent::__construct();
		
		//initialize class property
		$this->_table_prefix = '#__';		
	}
	

	//get the sports,comps,events,types & options for selection
	function getData($now_time, $sid, $cid, $eid) {
		//Lets load the content if it doesn't already exist
		if (empty($this->_data)) {

			//$this->_data['testvar'] = $compID;

			//$query = ' SELECT s.id as ttemid,t.name as tname,sport_id,tournament_type,UNIX_TIMESTAMP(start_time) as unixTime, ';
			
			//get sports and competitions
			$query = ' SELECT s.id AS sportID, sportName, name, c.created_date, c.id AS eventGroupId ';
			$query.= ' FROM '.$this->_table_prefix.'sport_name AS s ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'event_group AS c ON c.sport_id = s.id ';
			$query.= ' WHERE UNIX_TIMESTAMP(c.close_time) > '.$now_time ;
			$query.= ' ORDER BY sportName, name ASC ';
			//$query.= ' LIMIT 50';

			$sportNcomp_data = $this->_getList($query);
			$this->_data['sportsNcomps'] = $sportNcomp_data;


			//get the sport/comp ids if not set
			if ($sid == 0) { $sid = $sportNcomp_data[0]->sportID; }
			if ($cid == 0) { $cid = $sportNcomp_data[0]->eventGroupId; }

			//get events/matches
			$query = ' SELECT e.id AS eventID, e.start_date AS eventStartTime, e.name AS eventName, e.event_id AS extEventId ';
			$query.= ' FROM '.$this->_table_prefix.'event AS e ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'event_group_event AS ege ON e.id = ege.event_id ';
			$query.= ' WHERE ege.event_group_id = '.$cid ;
			$query.= ' AND UNIX_TIMESTAMP(e.start_date) > '.$now_time ;
			$query.= ' ORDER BY e.start_date ASC ';

			$events_data = $this->_getList($query);
			$this->_data['events'] = $events_data;


			//get the event id if not set
			if ($eid == 0 && $events_data[0]->eventID) { $eid = $events_data[0]->eventID; }

			//get typesNoptions
			//$query = ' SELECT * ';
			$query = ' SELECT mt.name AS betType, s.name AS betSelection, sp.place_bet_dividend AS odds ';
			$query.= ' , s.bet_place_ref, s.bet_type_ref, s.external_selection_id, s.id AS selectionID ';

			$query.= ' FROM '.$this->_table_prefix.'market_type AS mt ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'market AS m ON mt.id = m.market_type_id  ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'selection AS s ON m.id = s.market_id ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'selection_price AS sp ON s.id = sp.selection_id ';
			$query.= ' WHERE m.event_id = '.$eid ;
			//$query.= ' ORDER BY sportName ASC LIMIT 20';

			$this->_data['typesNoptions'] = $this->_getList($query);
			

			// set the sport/comp/event ids
			$this->_data['ids'] = array($sid, $cid, $eid);
		}
		return $this->_data;
	}


	/**
	 * Load a single record from the tbdb_meeting table by ID for Api.
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getEventApi($event_id)
	{
		$db =& $this->getDBO();
		$query = "SELECT * FROM `tbdb_event`  WHERE event_id ='".$event_id."'";
		$db->setQuery($query);
	
		return $db->loadObject();
	}

    public function getEventDetailsApi($id)
    {
        $db =& $this->getDBO();
        $query = "SELECT * FROM `tbdb_event`  WHERE id ='".$id."'";
        $db->setQuery($query);

        return $db->loadObject();
    }
	
	public function getSelectionIDApi($event_id, $bet_place_ref)
	{
		$db =& $this->getDBO();
		$query = "SELECT id FROM `tbdb_selection` WHERE external_selection_id ='".$event_id."' AND bet_place_ref = '".$bet_place_ref."' ";
		
		$db->setQuery($query);
		$file = "/tmp/api.txt";
		$debug = "- Get Selection query: $query \n";
		file_put_contents($file, $debug, FILE_APPEND | LOCK_EX);
		
		return $db->loadObject();
	}
	
	
	
	public function getMatchIDApi($match_id)
	{
		$db =& $this->getDBO();
		$query = "SELECT id FROM `tbdb_event`  WHERE id ='".$match_id."'";
		$db->setQuery($query);
	
		return $db->loadObject();
	}
	

	public function getExternalIDsApi($selectionID)
	{
		$db =& $this->getDBO();
		$query = "SELECT external_selection_id, external_market_id, external_event_id FROM `tbdb_selection` WHERE id ='".$selectionID."'";
		$db->setQuery($query);
	
		return $db->loadObject();
	}


    // eww

    public function getSelectionDetailsApi($selectionID)
    {
        $db =& $this->getDBO();

        $query = " SELECT s.id as option_id, s.name as option_name, sp.win_odds as option_odds,
                    m.id as market_id, m.market_status as market_status, sp.line as market_line,
                    mt.id as market_type_id, mt.name as market_type_name,
                    e.id as event_id, e.name as event_name, e.start_date as event_start_time,
                    eg.id as competition_id, eg.name as competition_name, eg.start_date as competition_start_time,
                    ts.id as sport_id, ts.name as sport_name
                    FROM tbdb_selection as s

            inner join tbdb_selection_price as sp on sp.selection_id = s.id
            inner join tbdb_market as m on m.id = s.market_id
            inner join tbdb_market_type  as  mt on mt.id = m.market_type_id
            inner join tbdb_event as e on e.id = m.event_id
            inner join tbdb_event_group_event as ege on ege.event_id = e.id
            inner join tbdb_event_group as eg on eg.id = ege.event_group_id
            inner join tbdb_tournament_sport as ts on ts.id = eg.sport_id

            WHERE s.id ='".$selectionID."'

        ";

        $db->setQuery($query);

        return $db->loadObject();
    }


// ##############################################################
/// split up functions for the API

	private function mkDateQuery($date = NULL, $time_field) {
		if ($date && date('Y-m-d') != $date) {
			if (strtotime($date) < time()) {
				//date is in the past >> returns just on that date
				$dateQuery = ' WHERE '.$time_field.' LIKE "'.$date.'%" ' ;
			} else {
				//date is in the future >> returns from date to future
				$dateQuery = ' WHERE UNIX_TIMESTAMP('.$time_field.') > '.strtotime($date) ;
			}
		} else {
			//no date or date is today >> returns from now to future
			$dateQuery = ' WHERE UNIX_TIMESTAMP('.$time_field.') > '.time() ;
		}
		return $dateQuery;
	}

	function getSports($date = null) {
		//Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			//construct the date query string
			if ($date!='all') {
				$dateQuery = $this->mkDateQuery($date, 'c.close_time');
			}
			
			//$now_time = time();
			
			//get sports and competitions
			$query = ' SELECT s.id AS sportID, sportName ';
			$query.= ' FROM '.$this->_table_prefix.'sport_name AS s ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'event_group AS c ON c.sport_id = s.id ';
			$query.= $dateQuery;
			$query.= ' GROUP BY s.id ';
			$query.= ' ORDER BY sportName, name ASC ';

			//$sports_data = $this->_getList($query);
			$this->_data = $this->_getList($query);
		}
		return $this->_data;
	}

	function getSportAndComps($date = NULL, $sid = NULL) {
		//Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			//construct the date query string
			$dateQuery = $this->mkDateQuery($date, 'c.close_time');
			
			//select sports if ids set
			if ($sid) { $sportQuery = ' AND s.id IN ('.$sid.') ' ; }

			//get sports and competitions
			$query = ' SELECT s.id AS sportID, sportName, name, c.created_date, c.id AS eventGroupId ';
			$query.= ' , c.start_date, c.close_time ';
			$query.= ' FROM '.$this->_table_prefix.'sport_name AS s ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'event_group AS c ON c.sport_id = s.id ';
			$query.= $dateQuery;
			$query.= $sportQuery;
			$query.= ' ORDER BY sportName, name ASC ';
			$this->_data = $this->_getList($query);
		}
		return $this->_data;
	}


	function getEvents($limit=0, $cid=0, $date = NULL) {
		//Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			//get the comp id if not set
			if ($cid) { $compQuery = ' AND ege.event_group_id = '.$cid ; }
			
			//add limit if set
			if ($limit) { $limitQuery = ' LIMIT '.$limit ; }
			
			$dateQuery = $this->mkDateQuery($date, 'e.start_date');

			//get events/matches
			$query = ' SELECT e.id AS eventID, e.start_date AS eventStartTime ';
			$query.= ', e.name AS eventName, e.event_id AS extEventId ';
			//$query.= ', ege.event_group_id AS compID, AS compName ';
			$query.= ' FROM '.$this->_table_prefix.'event AS e ';
 			if ($cid) { $query.= ' INNER JOIN '.$this->_table_prefix.'event_group_event AS ege ON e.id = ege.event_id '; }
			$query.= $dateQuery;
			$query.= $compQuery;
			$query.= ' ORDER BY e.start_date ASC ';
			$query.= $limitQuery;
			$this->_data = $this->_getList($query);
		}
		return $this->_data;
	}

	function getTypesAndOptions($eid) {
		//Lets load the content if it doesn't already exist
		if (empty($this->_data)) {
			//get typesNoptions
			//$query = ' SELECT * ';
			$query = ' SELECT mt.name AS betType, s.name AS betSelection, sp.place_bet_dividend AS odds ';
			$query.= ' , s.bet_place_ref, s.bet_type_ref, s.external_selection_id, s.id AS selectionID ';

			$query.= ' FROM '.$this->_table_prefix.'market_type AS mt ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'market AS m ON mt.id = m.market_type_id  ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'selection AS s ON m.id = s.market_id ';
			$query.= ' INNER JOIN '.$this->_table_prefix.'selection_price AS sp ON s.id = sp.selection_id ';
			$query.= ' WHERE m.event_id = '.$eid ;
			//$query.= ' ORDER BY sportName ASC LIMIT 20';

			$this->_data = $this->_getList($query);
		}
		return $this->_data;
	}

	function getNextToJump($limit=0, $date = NULL) {
		// !TODO: not sure if this is needed??
		// might be useful in stand alone next to jump module of all sports etc.??
	}


}


