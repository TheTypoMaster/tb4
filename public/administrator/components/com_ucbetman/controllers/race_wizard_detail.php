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
class race_wizard_detailController extends JController
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

    $link = 'index.php?option=com_ucbetman&controller=race_wizard_detail&task=edit&cid[]='.$meetingID;

    //set redirect for the current controller
    $this->setRedirect( $link,$msg );
  }
  
  
  // createPC function - creates PC from selected Meeting in PC wizard
  function createPC ()
  {
    $db =& JFactory::getDBO();

    // Set up variables
    $meetingID = JRequest::getVar( 'meetingID', 0, 'GET' );
    $meetingCode = JRequest::getVar( 'meetingCode', 0, 'GET' );
   
    $buyIn = JRequest::getVar( 'buyin', 0, 'GET' );
    $gamePlay = JRequest::getVar( 'gameplay', 0, 'GET' );
    $startingBucks = JRequest::getVar( 'startbucks', 0, 'GET' );
    
    $prizeFormula = JRequest::getVar( 'prizeFormula', 0, 'GET' );
    $prizePool = JRequest::getVar( 'minPrizePool', 0, 'GET' );
    $placesPaid = JRequest::getVar( 'placespaid', 0, 'GET' );
        
    $maxEntrants = JRequest::getVar( 'maxentrants', 0, 'GET' );
    $maxTickets = JRequest::getVar( 'maxtickets', 0, 'GET' );
    
    $tournrelo = JRequest::getVar( 'tournrelo', 0, 'GET' );
    $tournParent = JRequest::getVar( 'tournparent', 0, 'GET' );
    $tournamentName = JRequest::getVar( 'tournname', 0, 'GET' );
    $tournInfo = JRequest::getVar( 'tournInfo', 0, 'GET' );
    
    $blWPE = JRequest::getVar( 'blWPE', 0, 'GET' );
    $blT = JRequest::getVar( 'blT', 0, 'GET' );
    $blQ = JRequest::getVar( 'blQ', 0, 'GET' );
    $blF = JRequest::getVar( 'blF', 0, 'GET' );
    $blE = JRequest::getVar( 'blE', 0, 'GET' );
    
    // $entryFee = JRequest::getVar( 'entryFee', 0, 'GET' );
    $autoCreateNew = JRequest::getVar( 'autoCreateNew', 0, 'GET' );
    
    // Check for future TAB meeting ID
    if($meetingCode == ""){
    	$db2 = & DatabaseConnectionFactory::getInstance( 'web6db3' );	
    	// get tab_meeting_id
	    $query  = " SELECT tab_meeting_id from meeting WHERE id = '$meetingID' ";
	    $db2->setQuery( $query );
	    $tabResult = $db2->loadAssoc();
	    $tab_meeting_id = $tabResult['tab_meeting_id'];
    }
        
    // Get entry fee
    $query  = " SELECT buy_in from jos_ucbetman_tournament_buyins WHERE tournament_value = '$buyIn' ";
    $db->setQuery( $query );
    $feeResult = $db->loadAssoc();
    $entryFee = $feeResult['buy_in'];
    
    // Check if meeting is already a PC
    $query  = " SELECT id, name from atp_meeting WHERE tab_meeting_id = '$tab_meeting_id' ";
    $db->setQuery( $query );
    $result = $db->loadAssoc();
    $meetID = $result['id'];
    $meetName = $result['name'];
   
    $meetParent = $meetID;

    // If thre are no tournaments on the TAB Meeting ID value
    if(!$result){
    	$db2 = & DatabaseConnectionFactory::getInstance( 'web6db3' );
    	
    	// Future TAB CODE tournaments
    	if($meetingCode != ""){  
   			
			// Save fields into variables
			$tab_meeting_id = $meetingCode;
			$meetingName = JRequest::getVar( 'meetingName', 0, 'GET' );
			$meetingEvents = "0";
			$meetingType = "No Data";
			
			// get meeting code type from TAB meeting ID
			$meetType = substr($tab_meeting_id, 1, 1);
   			switch ($meetType){
   				case "R":
   					$meetingType = "Galloping";
   					break;
   				case "G":
   					$meetingType = "Greyhounds";
   					break;
   				case "H":
   					$meetingType = "Harness";
   					break;
   			}
			$meetingTrack = "No Data";
			$meetingWeather = "No Data";
			$meetingDate = "";
			
    		// Create new table entry for meeting in Joomla ATP Table
      		$query  = " INSERT INTO `atp_meeting` (`id`, `tab_meeting_id`, `name`, `events`, `type`, `track`, `weather`, `date`, `atp`,`odds_type`, `checked_out`, `checked_out_time`, `ordering`, `published`) VALUES ";
      		$query .= " ('', '$tab_meeting_id', '$meetingName', '$meetingEvents', '$meetingType', '$meetingTrack', '$meetingWeather', '$meetingDate',1, 'NSW TAB', 0, '0000-00-00 00:00:00', 0, 0) ";
      		$db->setQuery( $query );
      		$result = $db->query();
      		$meetingID = mysql_insert_id();
      		
      		$msg = JText::_( 'Meeting data not available: Q:'. $query .',  R:' . $result );
   		} else {
		  // tournaments on current TAB data    	
   		    		
   		  // Get meeting record
	      $query  = " SELECT * from meeting WHERE id = '$meetingID' ";
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
	      $query  = " SELECT * from race WHERE meeting_id = '$meetingID' ";
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
	        $query2  = " SELECT * from runner WHERE race_id = '$raceID' ";
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
	
	       //   $debug_file = "/tmp/pcWizard";
	       //   $debug_message = "Runner Query: $query2\n\n";
	       //   file_put_contents($debug_file, $debug_message, FILE_APPEND | LOCK_EX);
	        }
	     }
   	  }	
   
      $tournamentType = 'Punters Challenge';
      $tournamentImage = 'tixlogo_pc.png';
      $startTime = $firstRaceTime;
      $endTime = $raceStartdatetime;
      $tabMeetingID = $tab_meeting_id;
      
      if($meetingCode != ""){  
   		// set default start and end dates from TAB meeting code
		$year = substr($tab_meeting_id, 3, 4);
		$month = substr($tab_meeting_id, 7, 2); 
		$day = substr($tab_meeting_id, 9, 2);
		$startTime = "$year-$month-$day 12:00:00";
		$endTime = "$year-$month-$day 18:00:00";
      }
      
      // add loop for each tournament value
      $tournValueArray = explode(",", $buyIn);

      foreach ($tournValueArray as $buyIn) {
        // Create tournament table entry for PC
        $msg = JText::_( 'PC tournament created: '. $result .' ' . $tabMeetingID.'MC'.$meetingCode );
        
        $tournQuery  = " INSERT INTO `jos_ucbetman_tournaments` (`id`, `name`, `tournament_type`, `sport`, `tournament_image`, `game_play`, `start_time`, `end_time`, `tab_meeting_id`, `pc_meeting_id`, `date`, `min_prize_pool`, `places_paid`, `tournament_value`, `starting_bbucks`, `paid`, `published`, `parentID`, `entryFee`, `autoCreateNew`, `tournInfo`, `prizeFormula`, `tournament_name`, `betlimit_wple`, `betlimit_t`, `betlimit_q`, `betlimit_f`, `betlimit_e`) ";
        $tournQuery .= " VALUES ('', '$meetingName', '$tournamentType', '$meetingType', '$tournamentImage', '$gamePlay', '$startTime', '$endTime' , '$tabMeetingID', '$meetingID', '$meetingDate' , '$prizePool', '$placesPaid', '$buyIn', '$startingBucks', '0', '1', '$tournParent', '$entryFee', '$autoCreateNew', '$tournInfo', '$prizeFormula', '$tournamentName', '$blWPE', '$blT', '$blQ', '$blF', '$blE')";
        $db->setQuery( $tournQuery );
        $tournResult = $db->query();
      }

    // if the meeting is in the atp_race table  
    }else{
			
      	$db2 = & DatabaseConnectionFactory::getInstance( 'web6db3' );
      	// Get current meeting data
      	$query  = " SELECT * from meeting WHERE id = '$meetingID' ";
      	$db2->setQuery( $query );
      	$this->_meetingdetails = $db2->loadAssoc();

      	// Save fields into variables
      	$tabMeetingID = $this->_meetingdetails['tab_meeting_id'];
      	$meetingName = $this->_meetingdetails['name'];
      	$meetingEvents = $this->_meetingdetails['events'];
      	$meetingType = $this->_meetingdetails['type'];
      	$meetingTrack = $this->_meetingdetails['track'];
      	$meetingWeather = $this->_meetingdetails['weather'];
      	$meetingDate = $this->_meetingdetails['date'];	

      	// get current race data
      	$query  = " SELECT * from race WHERE meeting_id = '$meetingID' ";
	    $db2->setQuery( $query );
	    $this->racedetails = $db2->loadObjectList();
	    
	    // get 1st and last race times
	    $raceCount = count($this->racedetails) - 1;
	    $first = "0";
	    $startTime = &$this->racedetails[$first]->start_datetime;
	    $endTime = &$this->racedetails[$raceCount]->start_datetime;
	    
	    // store tourn details in variables
	    $tournValueArray = explode(",", $buyIn);
      	$tournamentType = 'Punters Challenge';
      	$tournamentImage = 'tixlogo_pc.png';
      	      	
      	$msg = JText::_( 'Meeting is already in ATP Table. PC tournament record created: '. $result .' ' . $meetName );
      	
      	// Create tournament table entry for PC
      	foreach ($tournValueArray as $buyIn) {
        	$tournQuery  = " INSERT INTO `jos_ucbetman_tournaments` (`id`, `name`, `tournament_type`, `sport`, `tournament_image`, `game_play`, `start_time`, `end_time`, `tab_meeting_id`, `pc_meeting_id`, `date`, `min_prize_pool`, `places_paid`, `tournament_value`, `starting_bbucks`, `paid`, `published`, `parentID`, `entryFee`, `autoCreateNew`, `tournInfo`, `prizeFormula`, `tournament_name`, `betlimit_wple`, `betlimit_t`, `betlimit_q`, `betlimit_f`, `betlimit_e`) ";
        	$tournQuery .= " VALUES ('', '$meetingName', '$tournamentType', '$meetingType', '$tournamentImage', '$gamePlay', '$startTime', '$endTime' , '$tabMeetingID', '$meetingID', '$meetingDate' , '$prizePool', '$placesPaid', '$buyIn', '$startingBucks', '0', '1', '$tournParent', '$entryFee', '$autoCreateNew', '$tournInfo', '$prizeFormula', '$tournamentName', '$blWPE', '$blT', '$blQ', '$blF', '$blE')";
        	$msg .= JText::_( 'TQ:'. $tournQuery .', TR:' . $tournResult );
        	$db->setQuery( $tournQuery );
        	$tournResult = $db->query();
       }
    }
	$link = 'index.php?option=com_ucbetman&controller=race_wizard';

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
    JRequest::setVar( 'view', 'race_wizard_detail' );
    JRequest::setVar( 'layout', 'form'  );
    JRequest::setVar( 'hidemainmenu', 1);


    parent::display();

    // give me  the bet_options
    $model = $this->getModel('race_wizard_detail');
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

    $model = $this->getModel('race_wizard_detail');

    if ($model->store($post)) {
      $msg = JText::_( 'Racing Tournament Changes Saved' );
    } else {
      $msg = JText::_( 'Error Saving Tournament Changes' );
    }

    // Check the table in so it can be edited.... we are done with it anyway
  //	$model->checkin();
    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard',$msg );
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

    $model = $this->getModel('race_wizard_detail');
    if(!$model->delete($cid)) {
      echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
    }

    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard' );
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

    $model = $this->getModel('race_wizard_detail');
    if(!$model->publish($cid, 1)) {
      echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
    }

    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard' );
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

    $model = $this->getModel('race_wizard_detail');
    if(!$model->publish($cid, 0)) {
      echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
    }

    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard' );
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
    $model = $this->getModel('race_wizard_detail');
    // $model->checkin();
    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard' );
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
    $model = $this->getModel('race_wizard_detail');
    $model->move(-1);

    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard');
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
    $model = $this->getModel('race_wizard_detail');
    $model->move(1);

    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard');
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

    $model = $this->getModel('race_wizard_detail');
    $model->saveorder($cid, $order);

    $msg = 'New ordering saved';
    $this->setRedirect( 'index.php?option=com_ucbetman&controller=race_wizard', $msg );
  }
}
