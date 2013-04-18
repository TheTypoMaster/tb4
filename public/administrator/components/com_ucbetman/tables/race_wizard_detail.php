<?php
/**
* @version		$Id: weblink.php 6532 2007-02-08 16:19:16Z pasamio $
* @package		Joomla
* @subpackage	Weblinks
* @copyright	Copyright (C) 2005 - 2007 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* bet_options Table class
*
* @package		Joomla
* @since 1.0
*/
class Tablerace_wizard_detail extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;
	

	var $tournament_value = null;
	
	/**
	 * @var string
	 */
	//var $max_entrants = null;
	
	/**
	 * @var string
	 */
	//var $max_tickets = null;
	
	/**
	 * @var string
	 */
	var $min_prize_pool = null;
	
	/**
	 * @var string
	 */
	//	var $max_bets_user = null;
	
	/**
	 * @var string
	 */
	var $starting_bbucks = null;
	
	/**
	 * @var string
	 */
	var $parentID = null;

	/**
	 * @var string
	 */
	//	var $autoCreateNew = null;
	
	/**
	 * @var string
	 */
	var $tournInfo = null;
	
	/**
	 * @var string
	 */
	var $prizeFormula = null;

	/**
	 * @var string
	 */
	var $tournament_name = null;
	
	/**
	 * @var string
	 */
	var $betlimit_wple = null;
	
	/**
	 * @var string
	 */
	var $betlimit_t = null;
	
	/**
	 * @var string
	 */
	var $betlimit_q = null;
	
	/**
	 * @var string
	 */
	var $betlimit_f = null;
	
	/**
	 * @var string
	 */
	var $betlimit_e = null;
	
	/**
	 * @var boolean
	 */
	var $checked_out = 0;

	/**
	 * @var time
	 */
	var $checked_out_time = 0;


	/**
	 * @var int
	 */
	var $ordering = null;
	
	/**
	 * @var int
	 */
	// var $published = 1;
	
	/**
	 * @var int
	 */
	var $access = null;

	/**
	 * @var int
	 */
	var $archived = null;

  /**
	 * table_prefix - table prefix for all component table
	 * 
	 * @var string
	 */
	var $_table_prefix = null;
	
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	function Tablerace_wizard_detail(& $db) {
	
	  //initialize class property
	  $this->_table_prefix = 'jos_';
	  
		parent::__construct($this->_table_prefix.'ucbetman_tournaments', 'id', $db);
	}

	/**
	* Overloaded bind function
	*
	* @acces public
	* @param array $hash named array
	* @return null|string	null is operation was satisfactory, otherwise returns an error
	* @see JTable:bind
	* @since 1.5
	*/

	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] )) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since 1.0
	 */
	function check()
	{
		/** check for valid name */
		if (trim($this->descript) == '') {
			$this->_error = JText::_('YOUR Race Tournament must contain a name.');
			return false;
		}


		/** check for existing name */
		$query = 'SELECT id FROM '.$this->_table_prefix.'atp_meeting WHERE name = "'.$this->name;
		$this->_db->setQuery($query);

		$xid = intval($this->_db->loadResult());
		if ($xid && $xid != intval($this->id)) {
			$this->_error = JText::sprintf('WARNNAMETRYAGAIN', JText::_('Teams'));
			return false;
		}
		return true;
	}
}
?>
