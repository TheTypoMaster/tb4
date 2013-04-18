<?php
/**
* @package user_manager_detail
* @version 1.5
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import MODEL object class
jimport('joomla.application.component.model');


class user_manager_detailModeluser_manager_detail extends JModel
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
			$user_mananger_detail = & $this->getTable();
			
		//	print_r($atp_wizard_detail);
			
			if(!$user_mananger_detail->checkout($uid, $this->_id)) {
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
			$user_mananger_detail = & $this->getTable();
			if(! $user_mananger_detail->checkin($this->_id)) {
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
			$user_id = JRequest::getVar( 'cid', '', 'GET' );
			$user_id = $user_id[0];
			//$user 	=& JFactory::getUser();
			//$user_id = $user->id;
			
			$query = 'SELECT * FROM jos_users AS u ';
			$query .= ' LEFT JOIN jos_ucbetman_user_ext AS e ON u.id = e.user_id';
			$query .= ' WHERE e.id = '. $user_id;
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
			return (boolean) $this->_data;
		}
		return true;
	}

	/**
	 * Method to initialise the user_manager_detail data
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
			$detail->name					= 0;
			$detail->username				= 0;
			$detail->email					= 0;
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

	


			
}

?>
