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
class Tablejuser_details extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $name = null;
	//@var string
	var $username = null;
	//@var string

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
	function Tablejuser_details(& $db) {
	
	  //initialize class property
	  $this->_table_prefix = '';
	  
		parent::__construct($this->_table_prefix.'jos_users', 'id', $db);
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
			$this->_error = JText::_('YOUR ATP must contain a name.');
			return false;
		}


		/** check for existing name */
		$query = 'SELECT id FROM '.$this->_table_prefix.'jos_users  WHERE name = "'.$this->name;
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
