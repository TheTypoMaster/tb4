<?php
/**
 * Joomla! 1.5 component uc_betman
 *
 * @version $Id: uc_betman.php 2009-08-07 04:40:27 svn $
 * @author uc-joomla.net
 * @package Joomla
 * @subpackage uc_betman
 * @license Copyright (c) 2009 - All Rights Reserved
 *
 * sports tournament betting component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class tournament_wizardModeltournament_wizard extends JModel {
    function __construct() {
    	

		
		parent::__construct();
    }
	
		/**
	 * Method to get a placeBetOptions data
	 *
	 * this method is called from the owner VIEW by VIEW->get('Data');
	 * - get list of all placeBetOptions for the current data page.
	 * - pagination is spec. by variables limitstart,limit.
	 * - ordering of list is build in _buildContentOrderBy  	//#### need to chk this out 	 	  	 
	 * @since 1.5
	 */

	function getSports() {
		$query = 'SELECT id, name ';
		$query.= 'FROM #__ucsm_sports ';
//		$query.= 'WHERE tb.id = '.$tournBetID;
		$this->_sports = $this->_getList($query);
		
		return $this->_sports;
	}

	function getLeagues() {
		$query = 'SELECT id, name ';
		$query.= 'FROM #__ucsm_leagues ';
//		$query.= 'WHERE tb.id = '.$tournBetID;
		$this->_leagues = $this->_getList($query);
		
		return $this->_leagues;
	}

	function getTournTypes() {
		$query = 'SELECT id, name ';
		$query.= 'FROM #__ucsm_tournament_types ';
//		$query.= 'WHERE tb.id = '.$tournBetID;
		$this->_tournTypes = $this->_getList($query);
		
		return $this->_tournTypes;
	}

	function getTournValues() {
		$query = 'SELECT id, name ';
		$query.= 'FROM #__ucsm_tournament_values ';
//		$query.= 'WHERE tb.id = '.$tournBetID;
		$this->_tournValues = $this->_getList($query);
		
		return $this->_tournValues;
	}

	function getBetTypes() {
		$query = 'SELECT id, name ';
		$query.= 'FROM #__ucsm_bet_types ';
//		$query.= 'WHERE tb.id = '.$tournBetID;
		$this->_betTypes = $this->_getList($query);
		
		return $this->_betTypes;
	}


	function getTeams() {
		$query = 'SELECT id, name ';
		$query.= 'FROM #__ucsm_teams ';
//		$query.= 'WHERE tb.id = '.$tournBetID;
		$this->_teams = $this->_getList($query);
		
		return $this->_teams;
	}


	function getData() {
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_data)) {

			//$now_time=date('g:i a - d M y');
			$now_unixtime=time();
/*
		    $tournBetID = JRequest::getVar( 'tournBetID', '', 'GET' );
			
			if ($tournTempVal == 0 || $tournTempVal == 'FREE') {
		    	$tournTempVal = 0;
		    	//$tournTempValue = 'FREE';
			}
		
			//main query
			$query = 'SELECT tb.id, tb.tournament_id, tb.tournament_value AS tournTempValue, tb.tournament_template_id AS tournTempID, ';
			$query.= 'tb.tournament_type_id, tb.start_time, tb.bet_type_odds_value, tb.current_bettabucks as bbucks, ';
			$query.= 'sport_id,bet_type_template_ids, ';
			$query.= 'sports.id,sports.name as sportName, ';
			$query.= 'tourn.id AS tournID,tourn.name as tournName, tourn.type_opts_avail, tourn.current_entrants AS currentEntrants,  ';
			$query.= 'UNIX_TIMESTAMP(tourn.start_time) as unixTime, ';
			$query.= 'ttype.id,ttype.name as tournTypeName, ttmpl.venue AS tournVenueName ';
			$query.= 'FROM #__ucsm_tournament_bets AS tb ';
			$query.= 'LEFT JOIN #__ucsm_tournament_templates AS ttmpl ON tb.tournament_template_id = ttmpl.id ';
			$query.= 'LEFT JOIN #__ucsm_tournaments AS tourn ON tb.tournament_id = tourn.id ';
			$query.= 'LEFT JOIN #__ucsm_tournament_types AS ttype ON tb.tournament_type_id=ttype.id ';		
			$query.= 'LEFT JOIN #__ucsm_sports AS sports ON sport_id=sports.id ';
			$query.= 'WHERE tb.id = '.$tournBetID;
			$this->_data = $this->_getList($query);


		    // put the bet_types db call here after grabbing the bet_types array
			$bet_type_temp_array = $this->_data[0];
			//$tourn_temp_id = $bet_typeVals->ttemid;
			$bet_type_temp_ids = $bet_type_temp_array->bet_type_template_ids;
			$query = " SELECT btt.id,bet_type_id, ";
			$query.= " bt.id,bt.name AS betTypeName ";
			$query.= " FROM #__ucsm_bet_type_templates AS btt ";
			$query.= ' LEFT JOIN #__ucsm_bet_types AS bt ON bet_type_id = bt.id ';
			$query.= " WHERE btt.id IN ($bet_type_temp_ids) ";		
			//$query.= " ORDER BY btt.ordering ";		
			$query.= " ORDER BY FIELD(btt.id, $bet_type_temp_ids) ";		
			//$query.= " AND tournament_template_id = $tourn_temp_id";		
			$this->_btypes = $this->_getList($query);
	
	        $bet_type_ids_array = split("," , $bet_type_temp_ids);
	        //$numtypes = count($bet_type_ids_array);
	
	        //$this->_numtypes = $db->getNumRows();
	        $this->_numtypes = $bet_type_ids_array;



			// ### stuff for HEADER vals - incorp. into above later
			$db = JFactory::getDBo();
			
			// get tournament id
			$query  = ' SELECT tournament_id ';
			$query .= ' FROM #__ucsm_tournament_bets';
			$query .= ' WHERE id = '.$tournBetID;
			$db->setQuery( $query );
			$tournament_id = $db->loadResult();
			
			// get tournament entrants
			$query  = ' SELECT tournament_value, current_entrants ';
			$query .= ' FROM #__ucsm_tournaments ';
			$query .= ' WHERE id = '.$tournament_id;
			$db->setQuery( $query );
			$current_entrants_plus = $db->loadRow();
			$tournament_value = $current_entrants_plus[0];
			$current_entrants = $current_entrants_plus[1];
			
			// Get places paid 
			$query  = ' SELECT places_paid AS placesPaid, pay_perc ';
			$query .= ' FROM #__ucsm_places_paid ';
			$query .= ' WHERE entrants > '.$current_entrants;
			$query .= ' LIMIT 1 ';
			$this->_ppaid = $this->_getList($query);
				
	        $this->_data = array(data => $this->_data,btypes => $this->_btypes,numtypes => $this->_numtypes,ppaid => $this->_ppaid);
	        	
		*/
		}
	
		
		
		return $this->_data;
	}
	
}
?>