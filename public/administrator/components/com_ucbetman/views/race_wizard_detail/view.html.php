<?php
/**
* @package race_wizard
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
class race_wizard_detailVIEWrace_wizard_detail extends JView
{
	/**
	 * Display the view
	 */
function display($tpl = null)
	{
	
		global $mainframe, $option;
		
		

		//DEVNOTE: Get URL, User,Model
		$uri 		=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();

		$lists = array();


		//get the race_wizard data
		$detail	=& $this->get('data');
		
		$meetingID = $detail->tab_meeting_id;
		$tournID = $detail->tournID;
	
		//DEVNOTE: set document title
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TopBetta Administration - Edit Racing Tournament: ' .$tournID ) );	
    	
		//DEVNOTE: Set ToolBar title
    	JToolBarHelper::title(   JText::_( 'Race Tournament Wizard' ), 'generic.png' );
		
    	//DEVNOTE: the new record ?  Edit or Create?
		$isNew		= ($detail->id < 1);

		// fail if checked out not by 'me'
		if ($model->isCheckedOut( $user->get('id') )) {
			$msg = JText::sprintf( 'DESCBEINGEDITTED', JText::_( 'THE DETAIL' ), $detail->name );
			$mainframe->redirect( 'index.php?option='. $option, $msg );
		}

		// Set toolbar items for the page
		$text = $isNew ? JText::_( 'Create NEW Racing Tournament' ) : JText::_( 'EDIT Existing Racing Tournament: '.$tournID );
		if($isNew){
			$this->setLayout('form_new');
			$meetings = $model->getMeetings();
			$this->assignRef( 'meetings',	$meetings );
			$tournParents = $model->getParentTourns();
			$this->assignRef( 'tournparents',	$tournParents );
			$getTournamentVals = $model->getTournamentVals();
			$this->assignRef( 'tournvalues',	$getTournamentVals );
			
		}else{
			$this->setLayout('form_edit');
			$races = $model->getRaces();
			$this->assignRef( 'races',	$races );
			
			$allBets = $model->getAllBets($meetingID);
			$this->assignRef( 'allBets',	$allBets);
			$raceBets = $model->getRaceBets($meetingID);
			$this->assignRef( 'raceBets',	$raceBets);
			$totalBets = $model->getTotalBets($meetingID);
			$this->assignRef( 'totalBets',	$totalBets);
			$totalBetters = $model->getTotalBetters($meetingID);
			$this->assignRef( 'totalBetters',	$totalBetters);
			$top10Runners = $model->getTop10Runners($meetingID);
			$this->assignRef( 'top10Runners',	$top10Runners );
			$tournWinners = $model->getTournWinners($tournID);
			$this->assignRef( 'tournWinners',	$tournWinners);
			
					
		}
		
		JToolBarHelper::title(   JText::_( 'Racing Tournament Wizard' ).': <small><small>[ ' . $text.' ]</small></small>' );
		// JToolBarHelper::save();
		if ($isNew)  {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::save( 'save', 'Save Changes');
			JToolBarHelper::cancel( 'cancel', 'Discard Changes' );
			
		}
		JToolBarHelper::help( 'screen.race_wizard.edit' );

		// Edit or Create?
		if (!$isNew)
		{
		  	//EDIT - check out the item
		 	$model->checkout( $user->get('id') );
			
			// build the html select list for ordering
			$query = 'SELECT ordering AS value, name AS text FROM atp_meeting WHERE id = '
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
		}

		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $detail->published );

		//clean race_wizard data
		jimport('joomla.filter.filteroutput');	
		JFilterOutput::objectHTMLSafe( $detail, ENT_QUOTES, 'description' );
		
		$this->assignRef('lists',			$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri->toString());

		parent::display($tpl);
	}

}

?>
