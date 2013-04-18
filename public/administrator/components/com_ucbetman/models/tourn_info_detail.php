<?php
/**
* @package bet_options
* @version 1.5
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import MODEL object class
jimport('joomla.application.component.model');


class tourn_info_detailModeltourn_info_detail extends JModel
{
	/**
	 * atp_wizard_detail id
	 *
	 * @var int
	 */
	var $_id = null;

	/**
	 * atp_wizard_detail data
	 *
	 * @var array
	 */
	var $_data = null;

  /**
	 * table_prefix - table prefix for all component table
	 * 
	 * @var string
	 */
	var $_table_prefix = null;
	
	/**
	 * Constructor
	 *
	 *	set id of bet_options detail 
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		//initialize class property
	  $this->_table_prefix = '#__ucsm_';		
	  
		$array = JRequest::getVar('cid',  0, '', 'array');
		$this->setId((int)$array[0]);

	}

	/**
	 * Method to set the atp_wizard_detail identifier
	 *
	 * @access	public
	 * @param	int atp_wizard_detail identifier
	 */
	function setId($id)
	{
		// Set atp_wizard_detail id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	
	function getTournamentVals() {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		
		// Setup dbo object	
	    $db		=& JFactory::getDBO();
		
		$query  = " SELECT * ";
		$query .= " FROM jos_ucbetman_tournament_buyins ";
		$db->setQuery($query);
		$result = $db->LoadObjectList();

		return $result;
	}
	
	
	function getMeetings()
	{
		if (empty($this->_meetings)) {
//			$option['driver']   = 'mysql';        // Database driver name
//			$option['host']     = 'dbweb';    // Database host name
//			$option['user']     = 'web6u2';       // User for database authentication
//			$option['password'] = 'U1s0ulnzsS';   // Password for database authentication
//			$option['database'] = 'web6db3';      // Database name
//			$option['prefix']   = '';             // Database prefix (may be empty)
//			$db2 = & JDatabase::getInstance( $option );
			$db2 = & DatabaseConnectionFactory::getInstance( 'web6db3' );
			
			// setup variables
			$date=strtotime('today 00:00');
			$nowTime = strtotime('now');
			$query  = " SELECT * from meeting WHERE date >= $date";
			$db2->setQuery( $query );
			$this->_meetings = $db2->loadObjectList();
		}
		return $this->_meetings;
	}

	function getParentTourns()
	{
		if (empty($this->_tournparent)) {
			$db		=& JFactory::getDBO();
			// setup variables
			$date=strtotime('today 00:00');
			$nowTime = strtotime('now');
			$query  = " SELECT * from jos_ucbetman_tournaments WHERE game_play = 'Jackpot'";
			$db->setQuery( $query );
			$this->_tournparent = $db->loadObjectList();
		}
		return $this->_tournparent;
	}


	function getRaces()
	{
		if (empty($this->_races)) {
			
			// setup variables
			$meeting_id = JRequest::getVar( 'cid', '', 'GET' );
			$meetingID = $meeting_id[0];
			
			//get atp meeting ID.
			$query  = " SELECT pc_meeting_id from jos_ucbetman_tournaments WHERE id = '$meetingID' LIMIT 1";
			$this->_db->setQuery( $query );
			$meetingID = $this->_db->loadResult();
			
			//if($meeting_id){
				$date=strtotime('today 00:00');
				$nowTime = strtotime('now');
				$query  = " SELECT * from atp_race WHERE meeting_id = '$meetingID' ORDER BY start_unixtimestamp ASC";
				$this->_db->setQuery( $query );
				$this->_races = $this->_db->loadObjectList();
			//}
		}
		return $this->_races;
	}
	
	function getAllBets($meetingID) {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		$currentUser = $user->get('id');
	
		// Setup dbo object	
	    $db		=& JFactory::getDBO();
				
		$nowDate=strtotime('today 00:00');
		
		$nowTime=strtotime('now');		
		
		$query = "SELECT sum(bet_amount) as betAllTotal FROM jos_ucbetman_tournament_bet_parent WHERE `tab_race_id` LIKE '%$meetingID%' ";
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	function getRaceBets($meetingID) {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		$currentUser = $user->get('id');
	
		// Setup dbo object	
	    $db		=& JFactory::getDBO();
				
		$nowDate=strtotime('today 00:00');
		
		$nowTime=strtotime('now');		
	
		$query = "SELECT tab_race_id, count(tab_race_id) as betCount, sum(bet_amount) as betTotal FROM jos_ucbetman_tournament_bet_parent WHERE `tab_race_id` LIKE '%$meetingID%' GROUP BY tab_race_id ";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
	
	function getTotalBets($meetingID) {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		$currentUser = $user->get('id');
	
		// Setup dbo object	
	    $db		=& JFactory::getDBO();
		
		

		$query = "SELECT count(`tab_race_id`) as totalBets FROM `jos_ucbetman_tournament_bet_parent`  WHERE `tab_race_id` LIKE '%$meetingID%' ";
		$db->setQuery($query);
		$result = $db->loadAssoc();
		
		
		return $result['totalBets'];
	}


	function getCurrentPrizePool($tournID) {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		$currentUser = $user->get('id');
	
		// Setup dbo object	
	    $db		=& JFactory::getDBO();
		
	    // get tournament details 
	    $query  = " SELECT tournament_value, current_entrants, entryFee from jos_ucbetman_tournaments WHERE id = $tournID ";
    	$db->setQuery( $query );
    	$feeResult = $db->loadAssoc();
    	
    	$tournament_value = $feeResult['tournament_value'];
    	$entrants = $feeResult['current_entrants'];
    	$entryFee = $feeResult['entryFee'];
    	
    	$currentPrizePool[0] = $entrants * $tournament_value;
    	$currentPrizePool[1] = $entryFee * $entrants;
     	
  		return $currentPrizePool;
  		
	}

	
	
	function getTotalBetters($tournID) {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		$currentUser = $user->get('id');
	
		// Setup dbo object	
	    $db		=& JFactory::getDBO();

		$query  = " SELECT distinct bp.user_id ";
		$query .= " FROM `jos_ucbetman_tournament_bet_parent`as bp ";
		$query .= " LEFT JOIN #__ucbetman_tournament_tickets AS tt ON tt.id = bp.ticket_id ";
		$query .= " WHERE tt.tournament_id = '$tournID' ";
				
		$db->setQuery($query);
		$result = $db->LoadObjectList();

		return $result;
	}

	function getTop10Runners($meetingID) {
		
		global $mainframe;
		
		// ### get jUser object
		$user =& JFactory::getUser();
		$currentUser = $user->get('id');
	
		// Setup dbo object	
	    $db		=& JFactory::getDBO();

		$query = "SELECT tb.selections, sum(tbp.bet_amount) as betAllTotal FROM jos_ucbetman_tournament_bet_parent AS tbp";
		$query .= " LEFT JOIN jos_ucbetman_tournament_bets AS tb ON tbp.id = tb.bet_parent ";
		$query .= " WHERE `tab_race_id` LIKE '%$meetingID%' group by tb.selections ORDER BY betAllTotal DESC LIMIT 10";
		$db->setQuery($query);
		$result = $db->LoadObjectList();

		return $result;
	}
	
	function getTournWinners($tournid) {
 		$db 	=& JFactory::getDBO();


		// Get tournment winners
		$query = "SELECT places_paid FROM jos_ucbetman_tournaments ";
		$query .= " WHERE `id` = '$tournid' LIMIT 1";
		$db->setQuery($query);
		$result = $db->LoadResult();
		

		// build query to get mybucks for pc players				
		$query  = ' SELECT u.bs_nickname as nickName, tt.myleaderboard as pBucks, users.username as userPIN, users.email as userEmail ';
		$query .= ' FROM #__ucbetman_user_ext AS u ';
		$query .= ' LEFT JOIN #__ucbetman_tournament_tickets as tt ON tt.user_id = u.user_id ';
		$query .= " LEFT JOIN jos_users as users ON u.user_id = users.id";
		$query .= ' WHERE u.user_id != "NULL"';
		$query .= " AND tt.tournament_id = '$tournid' " ;
		$query .= ' ORDER BY tt.myleaderboard DESC, u.bs_nickname ASC LIMIT '.$result;		
		// $query .= ' LIMIT '.$numberToList;		
		
		$db->setQuery( $query );
		$this->_tourndetails = $db->loadObjectList();
		return $this->_tourndetails;
	}
	
	/**
	 * Method to get a bet_options data
	 *
	 * this method is called from the owner VIEW by VIEW->get('Data');
	 * -  get detail of bet_options.
	 * - 	if detail exists then load else int a new detail 
	 * -  check if the country is published if not raise exception.
	 * @since 1.5
	 */
	function &getData()
	{
		//DEVNOTE:  Load the atp_wizard_detail data
		if ($this->_loadData())
		{

		////DEVNOTE: Check to see if the country is published
		//	if (!$this->_data->cat_pub) 
		//	{
		//		JError::raiseError( 404, JText::_("COUNTRY IS NOT PUBLISHED") );
		//		return;
		//	}
		}
		//DEVNOTE: init a new detail
		else  $this->_initData();

   	return $this->_data;
	}
	
	/**
	 * Method to checkout/lock the atp_wizard_detail
	 *
	 * @access	public
	 * @param	int	$uid	User ID of the user checking the helloworl detail out
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkout($uid = null)
	{
		if ($this->_id)
		{
			// Make sure we have a user id to checkout the article with
			if (is_null($uid)) {
				$user	=& JFactory::getUser();
				$uid	= $user->get('id');
			}
			// Lets get to it and checkout the thing...
			$atp_wizard_detail = & $this->getTable();
			
		//	print_r($atp_wizard_detail);
			
			if(!$atp_wizard_detail->checkout($uid, $this->_id)) {
				$this->setError($atp_wizard_detail);
				return false;
			}

			return true;
		}
		return false;
	}
	/**
	 * Method to checkin/unlock the atp_wizard_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function checkin()
	{
		if ($this->_id)
		{
			$atp_wizard_detail = & $this->getTable();
			if(! $atp_wizard_detail->checkin($this->_id)) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		return false;
	}	
	/**
	 * Tests if atp_wizard_detail is checked out
	 *
	 * @access	public
	 * @param	int	A user id
	 * @return	boolean	True if checked out
	 * @since	1.5
	 */
	function isCheckedOut( $uid=0 )
	{
		if ($this->_loadData())
		{
			if ($uid) {
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			} else {
				return $this->_data->checked_out;
			}
		}
	}	
		
	/**
	 * Method to load content atp_wizard_detail data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _loadData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = 'SELECT *, t.id as tournID FROM jos_ucbetman_tournaments AS t ';
			$query .= ' LEFT JOIN atp_meeting AS m ON m.id = t.pc_meeting_id';
			$query .= ' WHERE t.id = '. $this->_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the atp_wizard_detail data
	 *
	 * @access	private
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function _initData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$detail = new stdClass();
			$detail->id						= 0;
			$detail->tab_meeting_id					= 0;
			$detail->name				= 0;
			$detail->sport		= 0;
			$detail->start_time				= 0;
			$detail->end_time		= 0;
			$detail->tournament_type	= 0;
			$detail->game_play	= 0;
			$detail->tournament_values	= 0;
			$detail->current_entrants	= 0;
			$detail->checked_out			= 0;
			$detail->checked_out_time		= 0;
			$detail->ordering	 			= 0;
			$detail->published	 			= 0;
			$this->_data				 	= $detail;
			return (boolean) $this->_data;
		}
		return true;
	}
  	

	/**
	 * Method to store the helloword text
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function store($data)
	{
	 	//DEVNOTE: give me JTable object			 	
		$row =& $this->getTable();

		//DEVNOTE: Bind the form fields to the bet_options table
		if (!$row->bind($data)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		//DEVNOTE: Create the timestamp for the date field
		// $row->date = gmdate('Y-m-d H:i:s');

		//DEVNOTE: if new item, order last in appropriate group
		//JTable implements method getNextOrder for new order number
		//..'select max(ordering) from _table where catid=$catid' 
		if (!$row->id) {
		//	$where = 'catid = ' . $row->catid ;
			$row->ordering = $row->getNextOrder ( $where );
		}

		//DEVNOTE: Make sure the bet_options table is valid
		//JTable return always true but there is space to put
		//our custom check method
		// if (!$row->check()) {
		//	$this->setError($this->_db->getErrorMsg());
		//	return false;
	//	}

		//DEVNOTE: Store the bet_options detail record into the database
		if (!$row->store()) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}
	/**
	 * Method to remove a atp_wizard_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function delete($cid = array())
	{
		$result = false;


		if (count( $cid ))
		{
			$cids = implode( ',', $cid );
			$query = 'DELETE FROM jos_ucbetman_tournaments WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	
		/**
	 * Method to (un)publish a atp_wizard_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			$cids = implode( ',', $cid );

			$query = 'UPDATE jos_ucbetman_tournaments'
				. ' SET published = ' . intval( $publish )
				. ' WHERE id IN ( '.$cids.' )'
				. ' AND ( checked_out = 0 OR ( checked_out = ' .$user->get('id'). ' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	


	/**
	 * Method to move a atp_wizard_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function move($direction)
	{
	//DEVNOTE: Load table class from com_bet_options01/tables/atp_wizard_detail.php 	
		$row =& $this->getTable();
		if (!$row->load($this->_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

//	echo $direction;
	
//	exit;	
  //DEVNOTE: call move method of JTABLE. 
  //first parameter: direction [up/down]
  //second parameter: condition
		if (!$row->move( $direction, ' published >= 0 ' )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Method to saveorder a atp_wizard_detail
	 *
	 * @access	public
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& $this->getTable();
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->catid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
		//DEVNOTE: reorder for each group(category)
			$row->reorder('catid = '.$group);
		}

		return true;
	}
		
}

?>
