<?php
/**
* @package user_manager
* @version 1.5
* @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* Joomla! is free software and parts of it may contain or be derived from the
* GNU General Public License or other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );


/**
 [controller]View[controller]
 */
class user_manager_detailVIEWuser_manager_detail extends JView
{
	/**
	 * Display the view
	 */
function display($tpl = null)
	{
	
		global $mainframe, $option;	
    //DEVNOTE: Set ToolBar title
    JToolBarHelper::title(   JText::_( 'BS User Manager' ), 'generic.png' );

		//DEVNOTE: Get URL, User,Model
		$uri 		=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

// 		$this->setLayout('form');

		$lists = array();


		//get the user_manager data
		$detail	=& $this->get('data');
	
    //DEVNOTE: the new record ?  Edit or Create?
		$isNew		= ($detail->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'THE DETAIL' ), $detail->name );
			$mainframe->redirect( 'index.php?option='. $option, $msg );
		}

		// Set toolbar items for the page
		$text = $isNew ? JText::_( 'Create NEW BS USER' ) : JText::_( 'EDIT Extended User Details ' );
		if($isNew){
			$this->setLayout('form_new');
			//$meetings = $model->getMeetings();
			//$this->assignRef( 'meetings',	$meetings );
		}else{
			$this->setLayout('form_edit');
			//$races = $model->getRaces();
			//$this->assignRef( 'races',	$races );
		}
		
		$testlist = JHTML::_('select.booleanlist',  'terms', '', $this->detail->tb_terms );
		//print_r($testlist);
		
		
		JToolBarHelper::title(   JText::_( 'User Manager' ).': <small><small>[ ' . $text.' ]</small></small>' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		JToolBarHelper::help( 'screen.user_manager.edit' );



		// Edit or Create?
		if (!$isNew)
		{
		  //EDIT - check out the item
		// 	$model->checkout( $user->get('id') );
			
			// build the html select list for ordering
			$query = 'SELECT e.ordering AS value, u.name AS text FROM jos_users AS u ';
			$query .= " LEFT JOIN jos_ucbetman_user_ext AS e ON u.id = e.user_id ";
			$query .= " WHERE e.user_id = ".(int)$detail->id.' ORDER BY ordering';
			
			//DEVNOTE: prepare ordering combobox - edit only 
			$lists['ordering'] 			= JHTML::_('list.specificordering',  $detail, $detail->id, $query, 1 );

		}
		else
		{
			// initialise new record
			$detail->published = 1;
			$detail->name 	= null;
			$detail->order 	= 0;
		//	$detail->catid 	= JRequest::getVar( 'catid', 0, 'post', 'int' );
		}


		// build list of countries
	//	$lists['catid'] 			= $this->ComponentCountry('catid', intval( $detail->catid ) );


		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $detail->published );

		//clean user_manager data
		jimport('joomla.filter.filteroutput');	
		JFilterOutput::objectHTMLSafe( $detail, ENT_QUOTES, 'description' );
		
		$this->assignRef('lists',			$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri->toString());





		parent::display($tpl);
	}

	/**
	* Select list of active categories for components
	*/
	/*
	 * Function is used in the site/administrator : duplicate in JElementCategory
	 */
	function ComponentCountry( $name, $active=NULL, $javascript=NULL, $order='descript', $size=1, $sel_cat=1 )
	{
			global $mainframe;
			
			$model	=& $this->getModel();
		  	
		//	$countries[] = JHTMLSelect::option( '0', '- '. JText::_( 'Select a Country' ) .' -' );
			$countries[] = JHTML::_('select.option', '0', '- '. JText::_( 'Select a Country' ) .' -' );
			
			$countries = array_merge( $countries, $model->getCountry($order) );

		
		if ( count( $countries ) < 1 ) {
			$mainframe->redirect( 'index.php?option=com_ucbetman', JText::_( 'YOU MUST CREATE A COUNTRY FIRST.' ) );
		}

//		$country = JHTMLSelect::genericList( $countries, $name, 'class="inputbox" size="'. $size .'" '. $javascript, 'value', 'text', $active );
		$country = JHTML::_('select.genericList', $countries, $name, 'class="inputbox" size="'. $size .'" '. $javascript, 'value', 'text', $active );
		
		return $country;
	}	

}

?>
