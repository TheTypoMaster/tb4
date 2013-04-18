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
class Tableuser_manager_detail extends JTable
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;


	/**
	 * @var string
	 */
	var $username = null;

	/**
	 * @var string
	 */
	var $email = null;
	
	/**
	 * @var string
	 */
	var $name = null;
		/**
	 * @var string
	 */

	var $tb_title = null;
	//@var string
	var $tb_namef = null;
	//@var string
	var $tb_namel = null;
	//@var string
	var $tb_dob = null;
	//@var date
	var $tb_mobile = null;
	//@var string
	var $tb_phone = null;
	//@var string
	var $tb_pin = null;
	//@var int
	var $tb_address = null;
	//@var string
	var $tb_suburb = null;
	//@var string
	var $tb_state = null;
	//@var string
	var $tb_country = null;
	//@var string
	var $tb_pcode = null;
	//@var int
	var $tb_promo = null;
	//@var string
	var $tb_howuhear = null;
	//@var string
	var $tb_details = null;
	//@var string
	var $tb_optbox = null;
	//@var tinyint
	var $tb_sitepref = null;
	//@var string
	var $tb_privacy = null;
	//@var tinyint
	var $tb_terms = null;
	//@var tinyint
	
	
	var $ordering = null;
	
	
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
	function Tableuser_manager_detail(& $db) {
	
	  //initialize class property
	  $this->_table_prefix = '';
	  
		parent::__construct($this->_table_prefix.'jos_ucbetman_user_ext', 'id', $db);
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
		$query = 'SELECT id FROM '.$this->_table_prefix.'jos_ucbetman_user_ext  WHERE name = "'.$this->name;
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
