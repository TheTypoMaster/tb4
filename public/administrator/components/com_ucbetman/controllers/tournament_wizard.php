<?php
/**
* @package UC Sport Manager
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
 * tournament_wizard  Controller
 *
 * @package		Joomla
 * @subpackage	bet_options
 * @since 1.5
 */
class tournament_wizardController extends JController
{

	/**
	 * Custom Constructor
	 */
	function __construct( $default = array())
	{
		parent::__construct( $default );

		// Register Extra tasks
		$this->registerTask( 'add'  , 	'edit' );
		
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
		JRequest::setVar( 'view', 'tournament_wizard' );
	//	JRequest::setVar( 'layout', 'default'  );
		JRequest::setVar( 'hidemainmenu', 1);


		parent::display();

		// give me  the bet_options
		$model = $this->getModel('tournament_wizard');
		$model->checkout();
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

		$model = $this->getModel('tournament_wizard');
	
		if ($model->store($post)) {
			$msg = JText::_( 'Tournament Saved' );
		} else {
			$msg = JText::_( 'Error Saving Tournament' );
		}

		// Check the table in so it can be edited.... we are done with it anyway
		$model->checkin();
		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard',$msg );
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

		$model = $this->getModel('tournament_wizard');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard' );
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

		$model = $this->getModel('tournament_wizard');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard' );
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

		$model = $this->getModel('tournament_wizard');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard' );
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
		$model = $this->getModel('tournament_wizard');
		$model->checkin();
		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard' );
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
		$model = $this->getModel('tournament_wizard');
		$model->move(-1);

		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard');
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
		$model = $this->getModel('tournament_wizard');
		$model->move(1);

		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard');
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

		$model = $this->getModel('tournament_wizard');
		$model->saveorder($cid, $order);

		$msg = 'New ordering saved';
		$this->setRedirect( 'index.php?option=com_sportman01&controller=tournament_wizard', $msg );
	}
	
	function display() {
		JRequest::setVar( 'view', 'tournament_wizard' );
		parent::display();
	}
}
