<?php
/**
 * Joomla! 1.5 component uc_betman
 *
 * @version $Id: view.html.php 2009-08-07 04:40:27 svn $
 * @author uc-joomla.net
 * @package Joomla
 * @subpackage uc_betman
 * @license Copyright (c) 2009 - All Rights Reserved
 *
 * sports tournament betting component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
class Uc_betmanViewDefault extends JView {
    function display($tpl = null) {
    		 	//DEVNOTE: we need these 2 globals			 
    global $mainframe, $context;
		
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('UC_BetManager - Tournament Wizard') );
   
   
    //DEVNOTE: Set ToolBar title
    JToolBarHelper::title(   JText::_( 'Tournament Wizard' ), 'generic.png' );
    
    //DEVNOTE: Set toolbar items for the page
 		JToolBarHelper::addNewX();
 		JToolBarHelper::editListX();		
		JToolBarHelper::deleteList();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		//DEVNOTE :preferences, $height='150', $width='570', $alt = 'Preferences', $path = '')

		JToolBarHelper::preferences('com_uc_betman', '250');		
		JToolBarHelper::help( 'screen.uc_betman.edit' );   
		
    //DEVNOTE: Get URL
		$uri	=& JFactory::getURI();
		
		//DEVNOTE:give me ordering from request
		$filter_order     = $mainframe->getUserStateFromRequest( $context.'filter_order',      'filter_order', 	  'ordering' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( $context.'filter_order_Dir',  'filter_order_Dir', '' );		
	
		
		//DEVNOTE:remember the actual order and column  
	  $lists['order'] 		= $filter_order;  
		$lists['order_Dir'] = $filter_order_Dir;

  	
		//DEVNOTE:Get data from the model
		$items			= & $this->get( 'Data');
		$total			= & $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );
		
    //DEVNOTE:save a reference into view	
    $this->assignRef('user',		JFactory::getUser());	
    $this->assignRef('lists',		$lists);    
  	$this->assignRef('items',		$items); 		
    $this->assignRef('pagination',	$pagination);
    $this->assignRef('request_url',	$uri->toString());
	
        parent::display($tpl);
    }
}
?>