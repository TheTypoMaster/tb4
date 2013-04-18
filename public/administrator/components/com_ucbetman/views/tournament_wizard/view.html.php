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

// Import Joomla! librarie
jimport( 'joomla.application.component.view');
class tournament_wizardViewtournament_wizard extends JView {
   
   /**
	 * Display the view
	 */
	function display($tpl = null)
	{
	
		global $mainframe, $option;	
    //DEVNOTE: Set ToolBar title
    JToolBarHelper::title(   JText::_( 'UC Sport Manager .01 MANAGER DETAIL' ), 'generic.png' );

		//DEVNOTE: Get URL, User,Model
		$uri 		=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$this->setLayout('default');

		$lists = array();


		//get the entry_wizard
		$detail	=& $this->get('data');
		
		
		$sports	=& $this->get('Sports');
		$teams	=& $this->get('Teams');
		$leagues	=& $this->get('Leagues');
		$tournTypes	=& $this->get('TournTypes');
		$tournValues	=& $this->get('TournValues');
		$betTypes	=& $this->get('BetTypes');
		
		$document = & JFactory::getDocument();
		
		//DEVNOTE: Add stylesheets to the document
//		$document->addStyleSheet('/administrator/components/com_uc_betman/css/rfnet.css');

		
		//Add javascript file
		$document->addScript( '/administrator/components/com_uc_betman/js/datetimepicker_css.js' );
		
		
		
	
    //DEVNOTE: the new record ?  Edit or Create?
		$isNew		= ($detail->id < 1);

		// fail if checked out not by 'me'
	//	if ($model->isCheckedOut( $user->get('id') )) {
	//		$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'THE DETAIL' ), $detail->name );
	//		$mainframe->redirect( 'index.php?option='. $option, $msg );
	//	}

		// Set toolbar items for the page
		$text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
		JToolBarHelper::title(   JText::_( 'Tournaments Wizard' ).': <small><small>[ ' . $text.' ]</small></small>' );
		JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			// for existing items the button is renamed `close`
			JToolBarHelper::cancel( 'cancel', 'Close' );
		}
		JToolBarHelper::help( 'screen.entry_wizard.edit' );


/*
		// Edit or Create?
		if (!$isNew)
		{
		  //EDIT - check out the item
			$model->checkout( $user->get('id') );
			
			// build the html select list for ordering
			$query = 'SELECT ordering AS value, name AS text FROM '.$model->_table_prefix.'entry_wizard WHERE id = '
			.(int)$detail->id.' ORDER BY ordering';
			
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
*/

		// build list of countries
	//	$lists['catid'] 			= $this->ComponentCountry('catid', intval( $detail->catid ) );


		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $detail->published );

		//clean entry_wizard data
		jimport('joomla.filter.filteroutput');	
		JFilterOutput::objectHTMLSafe( $detail, ENT_QUOTES, 'description' );
		
		$this->assignRef('lists',			$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri->toString());

		$this->assignRef('sports',		$sports);
		$this->assignRef('teams',		$teams);
		$this->assignRef('leagues',		$leagues);

		$this->assignRef('tournTypes',		$tournTypes);
		$this->assignRef('tournValues',		$tournValues);
		$this->assignRef('betTypes',		$betTypes);
		
		
		parent::display($tpl);
	}

}

?>