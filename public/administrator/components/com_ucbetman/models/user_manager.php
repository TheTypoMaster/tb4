<?php
/**
* @package user_manager
* @version 1.5
* 
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import MODEL object class
jimport('joomla.application.component.model');

/**
 * atp_wizard Component atp_wizard Model
 *
 * @author wojta <vojtechovsky@gmail.com>
 * @package		Joomla
 * @subpackage	atp_wizard
 * @since 1.5
 */
class user_managerModeluser_manager extends JModel
{

	/**
	 * atp_wizard data
	 *
	 * @var array
	 */
	var $_data = null;
	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

  /**
	 * table_prefix - table prefix for all component table
	 * 
	 * @var string
	 */
	var $_table_prefix = null;
	/**
	 * Constructor
	 *
	 * @since 1.5
	 */
	function __construct()
	{
		parent::__construct();

		global $mainframe, $context;
		
		//initialize class property
	  $this->_table_prefix = '#__ucbetman_';			

		//DEVNOTE: Get the pagination request variables
		$limit			= $mainframe->getUserStateFromRequest( $context.'limit', 'limit', $mainframe->getCfg('list_limit'), 0);
		$limitstart = $mainframe->getUserStateFromRequest( $context.'limitstart', 'limitstart', 0 );

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

	}
	
	
	/**
	 * Method to get a atp_wizard data
	 *
	 * this method is called from the owner VIEW by VIEW->get('Data');
	 * - get list of all atp_wizard for the current data page.
	 * - pagination is spec. by variables limitstart,limit.
	 * - ordering of list is build in _buildContentOrderBy  	 	 	  	 
	 * @since 1.5
	 */
	function getData()
	{
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
}

	/**
	 * Method to get the total number of tournament_templates items
	 *
	 * @access public
	 * @return integer
	 */
	function getTotal()
	{
		//DEVNOTE: Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}
	
	/**
	 * Method to get a pagination object for the tournament_templates
	 *
	 * @access public
	 * @return integer
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}
  	
	function _buildQuery()
	{
		$orderby	= $this->_buildContentOrderBy();

			$query  = ' SELECT * '; 
			$query .= ' FROM jos_users AS u ';
			$query .= ' LEFT JOIN jos_ucbetman_user_ext AS e ON u.id = e.user_id'  .$orderby;

		//$query = ' SELECT * '
		//	. ' FROM jos_users '.$orderby;
			/*. ' LEFT JOIN '.$this->_table_prefix.'tournament_templates AS c ON c.id = h.catid '.$orderby;*/

		return $query;
	}
	
	function _buildContentOrderBy()
	{
		global $mainframe, $context;

		//DEVNOTE:give me ordering from request
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order',      'filter_order', 	  'ordering' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',  'filter_order_Dir', '' );		

		//DEVNOTE:if users request was order by "ordering" then first order by description and then  by "ordering"
		//simple reason: tournament_templates are grouped by categories
		if ($filter_order == 'ordering'){
			$orderby 	= ' ORDER BY ordering ';
		} else {
			$orderby 	= ' ORDER BY ordering ';			
		}
  
		return $orderby;
	}

}	


?>

