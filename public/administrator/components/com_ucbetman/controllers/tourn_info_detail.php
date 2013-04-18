<?php
/**
* @package UC Bet Man
* @version 1.5
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import CONTROLLER object class
jimport( 'joomla.application.component.controller' );


/**
 * tournament_templates_detail  Controller
 *
 * @package		Joomla
 * @subpackage	bet_options
 * @since 1.5
 */
class tourn_info_detailController extends JController
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		$this->registerTask( 'stopRaceBets'  , 	'stopRaceBets' );
		
	}


	// Stop bets on race
	function stopRaceBets ()
	{
		$db =& JFactory::getDBO();
		$date=strtotime('today 00:00');
		// $nowTime = strtotime('now');
		// Set up variables
		// $meetingID = JRequest::getVar( 'meetingID', 0, 'GET' );
		$raceID = JRequest::getVar( 'raceID', 0, 'GET' );
		
		// UPDATE race start_unixtimestamp to current time to stop bets being taken
		$query  = " UPDATE atp_race SET start_unixtimestamp = '$date' WHERE id = '$raceID' ";
		$db->setQuery( $query );
		$result = $db->loadResult();
		
		// Get meetingID
		$query  = " SELECT meeting_id, name, number from atp_race WHERE id = $raceID ";
		$db->setQuery( $query );
		$raceDetails = $db->loadRow();
		$meetingID = $raceDetails[0];
		$raceName = $raceDetails[1];
		$raceNumber = $raceDetails[2];
		
		if($result){
			$msg = JText::_( 'FAILED: Bets NOT closed on Race '. $raceNumber .' : ' .$raceName);
		}else{
			$msg = JText::_( 'SUCCESS: Bets CLOSED on Race  '. $raceNumber .' : ' .$raceName);
		}
							
		$link = 'index.php?option=com_ucbetman&controller=tourn_info_detail&task=edit&cid[]='.$meetingID;
			
		//set redirect for the current controller
		$this->setRedirect( $link,$msg ); 
	}

	 
	function closeTournament ()
  {
    $db =& JFactory::getDBO();
	$user =&JFactory::getUser();
  	      
    $tournID = JRequest::getVar( 'tournID', 0, 'GET' );
	$closeText = JRequest::getVar( 'closeText', 0, 'GET' );
    
	// set admin_cancelled flag and text
    $query  = " UPDATE jos_ucbetman_tournaments SET admin_cancelled = '1', admin_cancelled_reason = '$closeText' WHERE id = '$tournID' ";
    $db->setQuery( $query );
    // $result = $db->loadResult();
	
    // get tournament details
    $query  = " SELECT id, tournament_value, entryFee from jos_ucbetman_tournaments WHERE id = $tournID ";
    $db->setQuery( $query );
    $tournDetails = $db->loadRow();
    $buyin = $tournDetails[1] * 100;
    $entryfee = $tournDetails[2] * 100;
    $totalEntry = $buyin + $entryfee;
    
	// get tournament entrants
    $query = " SELECT id, user_id from jos_ucbetman_tournament_tickets WHERE tournament_id = '$tournID' ";
    $db->setQuery( $query );
    $this->tournTickets = $db->loadObjectList();

    // loop on each tournament ticket.
    for ($i=0, $n=count( $this->tournTickets ); $i < $n; $i++) {
    	$ID = &$this->tournTickets[$i]->id;
        $userID = &$this->tournTickets[$i]->user_id;
			
        // refund entrants buyin and entry fee     
       // $setUserID = $user->tournament_dollars->setUserID($userID);
       // $refundUser = $user->tournament_dollars->increment($totalEntry, refund, "Tournament Cancelled. Entry Refunded");

        // set refunded flag on ticket record
        $query  = " UPDATE jos_ucbetman_tournament_tickets SET refunded = '1' WHERE id = '$ID' ";
	    $db->setQuery( $query );
	    $result = $db->loadResult();
    }
    
    $msg = JText::_( 'Debug:'.$currentUserId);
 	$link = 'index.php?option=com_ucbetman&controller=tourn_info';

    //set redirect for the current controller
    $this->setRedirect( $link,$msg );
  }
	
	
	
	//createPC function - creates PC from selected Meeting in PC wizard
	function createATP ()
	{
		$db =& JFactory::getDBO();
		
		// Set up variables
		$meetingID = JRequest::getVar( 'meetingID', 0, 'GET' );
		$buyIn = JRequest::getVar( 'buyin', 0, 'GET' );
		$gamePlay = JRequest::getVar( 'gameplay', 0, 'GET' );
		$startingBucks = JRequest::getVar( 'startbucks', 0, 'GET' );
		$prizePool = JRequest::getVar( 'prizepool', 0, 'GET' );
		$placesPaid = JRequest::getVar( 'placespaid', 0, 'GET' );
		$maxEntrants = JRequest::getVar( 'maxentrants', 0, 'GET' );
		$maxTickets = JRequest::getVar( 'maxtickets', 0, 'GET' );
		$tournrelo = JRequest::getVar( 'tournrelo', 0, 'GET' );
		$meetingCode = JRequest::getVar( 'meetingcode', 0, 'GET' );
		$tournParent = JRequest::getVar( 'tournparent', 0, 'GET' );
		
		// New Fields added to form
		$entryFee = JRequest::getVar( 'entryFee', 0, 'GET' );
		$autoCreateNew = JRequest::getVar( 'autoCreateNew', 0, 'GET' );
		$tournInfo = JRequest::getVar( 'tournInfo', 0, 'GET' );
		$prizeFormula = JRequest::getVar( 'prizeFormula', 0, 'GET' );
		
					
		
		// Check if meeting is already a PC
		$query  = " SELECT id, name from atp_meeting WHERE id = $meetingID ";
		$db->setQuery( $query );
		$result = $db->loadAssoc();
		$result = $result['id'];
		$meetName = $result['name'];
				
		if($result < "0" ){
			// Copy Meeting Table
			// connect to race database
			//$option['driver']   = 'mysql';        // Database driver name
			//$option['host']     = 'localhost';    // Database host name
			//$option['user']     = 'web6u2';       // User for database authentication
			//$option['password'] = 'U1s0ulnzsS';   // Password for database authentication
			//$option['database'] = 'web6db3';      // Database name
			//$option['prefix']   = '';             // Database prefix (may be empty)
			//$db2 = & JDatabase::getInstance( $option );
			$db2 = & DatabaseConnectionFactory::getInstance( 'web6db3' );
			
			// Get meeting record
			$query  = " SELECT * from web6db3.meeting WHERE id = '$meetingID' ";
			$db2->setQuery( $query );
			$this->_meetingdetails = $db2->loadAssoc();
					
			// Save fields into variables
			$tab_meeting_id = $this->_meetingdetails['tab_meeting_id'];
			$meetingName = $this->_meetingdetails['name'];
			$meetingEvents = $this->_meetingdetails['events'];
			$meetingType = $this->_meetingdetails['type'];
			$meetingTrack = $this->_meetingdetails['track'];
			$meetingWeather = $this->_meetingdetails['weather'];
			$meetingDate = $this->_meetingdetails['date'];
	
			// Create new table entry for meeting in Joomla ATP Table
			$query  = " INSERT INTO `atp_meeting` (`id`, `tab_meeting_id`, `name`, `events`, `type`, `track`, `weather`, `date`, `atp`,`odds_type`, `checked_out`, `checked_out_time`, `ordering`, `published`) VALUES ";
			$query .= " ('$meetingID', '$tab_meeting_id', '$meetingName', '$meetingEvents', '$meetingType', '$meetingTrack', '$meetingWeather', '$meetingDate',1, 'NSW TAB', 0, '0000-00-00 00:00:00', 0, 0) ";
			$db->setQuery( $query );
			$result = $db->query();
			
			// Copy Race Table
			// Get race records
			$query  = " SELECT * from web6db3.race WHERE meeting_id = '$meetingID' ";
			$db2->setQuery( $query );
			$this->racedetails = $db2->loadObjectList();
			
			for ($i=0, $n=count( $this->racedetails ); $i < $n; $i++) {
				$row = &$this->racedetails[$i];

				// Save fields into variables
				$raceID = $row->id;
				$meetingID = $row->meeting_id;
				$tab_race_id = $row->tab_race_id;
				$raceType = $row->type;
				$raceLocation = $row->location;
				$raceLocation = str_replace ("'", "\'", $raceLocation);
				$raceNumber = $row->number;
				$raceName = $row->name;
				$raceName = str_replace ("'", "\'", $raceName);
				$raceTime = $row->time;
				$raceDate = $row->date;
				$raceDistance = $row->distance;
				$raceClass = $row->class;
				$raceJump = $row->time2jump;
				$raceStatus = $row->status;
				$raceDump = $row->dump_timestamp;
				$raceStart = $row->start_unixtimestamp;
				$raceStartdatetime = $row->start_datetime;
				
		
				// Create new table entry for meeting in Joomla ATP Table
				$query1  = " INSERT INTO `atp_race` (`id`, `meeting_id`, `tab_race_id`, `type`, `location`, `number`, `name`, `time`, `date`, `distance`, `class`, `time2jump`, `status`, `dump_timestamp`, `start_unixtimestamp`, `start_datetime`) VALUES ";
				$query1 .= " ('$raceID', '$meetingID', '$tab_race_id', '$raceType', '$raceLocation', '$raceNumber', '$raceName', '$raceTime', $raceDate, '$raceDistance', '$raceClass', '$raceJump', '$raceStatus', '$raceDump', '$raceStart', '$raceStartdatetime') ";
				$db->setQuery( $query1 );
				$result1 = $db->query();
				
				if ($i == "0"){
					$firstRaceTime = $raceStartdatetime;
				}
				
				// Copy Runners Table
				// Get runner records
				$query2  = " SELECT * from web6db3.runner WHERE race_id = '$raceID' ";
				$db2->setQuery( $query2 );
				$this->runnerdetails = $db2->loadObjectList();
				
				for ($ii=0, $nn=count( $this->runnerdetails ); $ii < $nn; $ii++) {
					$row = &$this->runnerdetails[$ii];
	
					// Save fields into variables
					$runnerID = $row->id;
					$raceID = $row->race_id;
					$runnerNumber = $row->number;
					$runnerName = $row->name;
					$runnerName = str_replace ("'", "\'", $runnerName);
					$runnerAssociate = $row->associate;
					$runnerAssociate = str_replace ("'", "\'", $runnerAssociate);
					$runnerStatus = $row->status;
					$runnerBarrier = $row->barrier;
					$runnerHandicap = $row->handicap;
					$runnerIdent = $row->ident;
					$runnerDate = $row->date;
					$runnerTabRaceID = $row->tab_race_id;
					// $runnerOneRunnerID = $row->one_runner_id;
			
					// Create new table entry for meeting in Joomla ATP Table
					$query2  = " INSERT INTO `atp_runner` (`id`, `race_id`, `number`, `name`, `associate`, `status`, `barrier`, `handicap`, `ident`, `date`, `tab_race_id`) VALUES ";
					$query2 .= " ('$runnerID', '$raceID', '$runnerNumber', '$runnerName', '$runnerAssociate', '$runnerStatus', '$runnerBarrier', '$runnerHandicap', '$runnerIdent', $runnerDate, '$runnerTabRaceID') ";
					$db->setQuery( $query2 );
					$result2 = $db->query();
					
					$debug_file = "/tmp/pcWizard";	
					$debug_message = "Runner Query: $query2\n\n";
					file_put_contents($debug_file, $debug_message, FILE_APPEND | LOCK_EX);
				}		
			}		
			$tournamentType = 'Punters Challenge';
			$tournamentImage = 'tixlogo_pc.png';
			$startTime = $firstRaceTime;
			$endTime = $raceStartdatetime;
			$tabMeetingID = $tab_meeting_id;
			// add loop for each tournament value
			$tournValueArray = explode(",", $buyIn);
			
			foreach ($tournValueArray as $buyIn) {									
				// Create tournament table entry for PC
				$tournQuery  = " INSERT INTO `jos_ucbetman_tournaments` (`id`, `name`, `tournament_type`, `sport`, `tournament_image`, `game_play`, `start_time`, `end_time`, `tab_meeting_id`, `pc_meeting_id`, `date`, `min_prize_pool`, `places_paid`, `tournament_value`, `starting_bbucks`, `paid`, `published`, `parentID`, `entryFee`, `autoCreateNew`, `tournInfo`, `prizeFormula`) ";
				$tournQuery .= " VALUES ('', '$meetingName', '$tournamentType', '$meetingType', '$tournamentImage', '$gamePlay', '$startTime', '$endTime' , '$tabMeetingID', '$meetingID', '$meetingDate' , '$prizePool', '$placesPaid', '$buyIn', '$startingBucks', '0', '1', '$tournParent', '$entryFee', '$autoCreateNew', '$tournInfo', '$prizeFormula')";
				$db->setQuery( $tournQuery );
				$tournResult = $db->query();
			}
											
		}else{
			$msg = JText::_( 'Meeting is already in ATP Table. ATP/PC tournament created only: '. $result .' ' . $meetName );
			// Create tournament table entry for PC
			foreach ($tournValueArray as $buyIn) {				
				$tournQuery  = " INSERT INTO `jos_ucbetman_tournaments` (`id`, `name`, `tournament_type`, `sport`, `tournament_image`, `game_play`, `start_time`, `end_time`, `tab_meeting_id`, `pc_meeting_id`, `date`, `min_prize_pool`, `places_paid`, `tournament_value`, `starting_bbucks`, `paid`, `published`, `parentID`, `entryFee`, `autoCreateNew`, `tournInfo`, `prizeFormula`) ";
				$tournQuery .= " VALUES ('', '$meetingName', '$tournamentType', '$meetingType', '$tournamentImage', '$gamePlay', '$startTime', '$endTime' , '$tabMeetingID', '$meetingID', '$meetingDate' , '$prizePool', '$placesPaid', '$buyIn', '$startingBucks', '0', '1', '$tournParent', '$entryFee', '$autoCreateNew', '$tournInfo', '$prizeFormula')";
				$db->setQuery( $tournQuery );
				$tournResult = $db->query();
			}
		}
		
		$link = 'index.php?option=com_ucbetman&controller=tourn_info';
			
		//set redirect for the current controller
		$this->setRedirect( $link,$msg ); 
	}
	
	/** function edit
	*
	* Create a new item or edit existing item 
	* 
	* 1) set a custom VIEW layout to 'form'  
	* so expecting path is : [componentpath]/views/[$controller->_name]/'form.php';			
  * 2) show the view
  * 3) get(create) MODEL and checkout item
	*/
	function edit()
	{
		JRequest::setVar( 'view', 'tourn_info_detail' );
		JRequest::setVar( 'layout', 'form'  );
		JRequest::setVar( 'hidemainmenu', 1);


		parent::display();

		// give me  the bet_options
		$model = $this->getModel('tourn_info_detail');
		// $model->checkout();
	}
      
	/** function save
	*
	* Save the selected item specified by id
	* and set Redirection to the list of items	
	* 		
	* @param int id - keyvalue of the item
	* @return set Redirection
	*/
	function save()
	{
	
		$post	= JRequest::get('post');
		$cid	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] = $cid[0];

		$model = $this->getModel('tourn_info_detail');
	
		if ($model->store($post)) {
			$msg = JText::_( 'Racing Tournamen Saved' );
		} else {
			$msg = JText::_( 'Error Saving Tournament' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
	//	$model->checkin();
		$this->setRedirect( 'index.php?option=com_ucbetman&controller=tourn_info',$msg );
}

	/** function remove
	*
	* Delete all items specified by array cid
	* and set Redirection to the list of items	
	* 		
	* @param array cid - array of id
	* @return set Redirection
	*/
	function remove()
	{
		global $mainframe;

		$cid = JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to delete' ) );
		}

		$model = $this->getModel('tourn_info_detail');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_ucbetman&controller=atp_wizard' );
	}
	
	/** function publish
	*
	* Publish all items specified by array cid
	* and set Redirection to the list of items	
	* 		
	* @param array cid - array of id
	* @return set Redirection
	*/
	function publish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to publish' ) );
		}

		$model = $this->getModel('tourn_info_detail');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_ucbetman&controller=atp_wizard' );
	}

	/** function unpublish
	*
	* Unpublish all items specified by array cid
	* and set Redirection to the list of items
	* 			
	* @param array cid - array of id
	* @return set Redirection
	*/
	function unpublish()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );

		if (!is_array( $cid ) || count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'Select an item to unpublish' ) );
		}

		$model = $this->getModel('tourn_info_detail');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_ucbetman&controller=tourn_info' );
	}	
	
	/** function cancel
	*
	* Check in the selected detail 
	* and set Redirection to the list of items	
	* 		
	* @return set Redirection
	*/
	function cancel()
	{
		// Checkin the detail
		$model = $this->getModel('tourn_info_detail');
		// $model->checkin();
		$this->setRedirect( 'index.php?option=com_ucbetman&controller=tourn_info' );
	}	
	

	/** function orderup
	*
	* change order up
	* and set Redirection to the list of items
	* 			
	* @param array cid - array of id
	* @return set Redirection
	*/
	function orderup()
	{
		$model = $this->getModel('tourn_info_detail');
		$model->move(-1);

		$this->setRedirect( 'index.php?option=com_ucbetman&controller=tourn_info');
	}


	/** function orderdown
	*
	* change order down
	* and set Redirection to the list of items
	* 			
	* @param array cid - array of id
	* @return set Redirection
	*/
	function orderdown()
	{
		$model = $this->getModel('tourn_info_detail');
		$model->move(1);

		$this->setRedirect( 'index.php?option=com_ucbetman&controller=tourn_info');
	}

	/** function saveorder
	*
	* saveorder of the bet_options items
	* 			
	* @param array cid		- array of id
	* @param array order	- array of order 
	* @return set Redirection
	*/
	function saveorder()
	{
		$cid 	= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(0), 'post', 'array' );

		$model = $this->getModel('tourn_info_detail');
		$model->saveorder($cid, $order);

		$msg = 'New ordering saved';
		$this->setRedirect( 'index.php?option=com_ucbetman&controller=tourn_info', $msg );
	}
}
