<?php
/**
* @package tourn_info
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
class tourn_info_detailVIEWtourn_info_detail extends JView
{
	/**
	 * Display the view
	 */
function display($tpl = null)
	{
	
		global $mainframe, $option;	
	    
		// Setup toolbar title
		$text = JText::_( 'Manage / View Tournament' );
	    // JToolBarHelper::title(   JText::_( 'Race Tournament Information' ), 'generic.png' );
	    JToolBarHelper::title(   JText::_( 'Racing Tournament Information' ).': <small><small>[ ' . $text.' ]</small></small>' );

	    // Set toolbar items for the page
	    //JToolBarHelper::save();
		JToolBarHelper::cancel( 'cancel', 'Close' );
		JToolBarHelper::help( 'screen.tourn_info.edit' );
		
		//DEVNOTE: Get URL, User,Model
		$uri 		=& JFactory::getURI();
		$user 	=& JFactory::getUser();
		$model	=& $this->getModel();
		$lists = array();
		//get the tourn_info data
		$detail	=& $this->get('data');
		
		$this->setLayout('default');
			
		$meetingID = $detail->tab_meeting_id;
		$tournID = $detail->tournID;

		//get the race_wizard data
		$detail	=& $this->get('data');
		$races = $model->getRaces();
		$this->assignRef( 'races',	$races );
		
		$allBets = $model->getAllBets($meetingID);
		$this->assignRef( 'allBets',	$allBets);
		
		$raceBets = $model->getRaceBets($meetingID);
		$this->assignRef( 'raceBets',	$raceBets);
		
		$totalBets = $model->getTotalBets($meetingID);
		$this->assignRef( 'totalBets',	$totalBets);
		
		$totalBetters = $model->getTotalBetters($tournID);
		$this->assignRef( 'totalBetters',	$totalBetters);
		
		$top10Runners = $model->getTop10Runners($meetingID);
		$this->assignRef( 'top10Runners',	$top10Runners );
		
		$tournWinners = $model->getTournWinners($tournID);
		$this->assignRef( 'tournWinners',	$tournWinners);
		
		$currentPrizePool = $model->getCurrentPrizePool($tournID);
		$this->assignRef( 'currentPrizePool',	$currentPrizePool);
	
		



		
			
			// build the html select list for ordering
			$query = 'SELECT ordering AS value, name AS text FROM atp_meeting WHERE id = '
			.(int)$detail->id.' ORDER BY ordering';
			
			//DEVNOTE: prepare ordering combobox - edit only 
			$lists['ordering'] 			= JHTML::_('list.specificordering',  $detail, $detail->id, $query, 1 );


		// build list of countries
	//	$lists['catid'] 			= $this->ComponentCountry('catid', intval( $detail->catid ) );


		// build the html select list
		$lists['published'] 		= JHTML::_('select.booleanlist',  'published', 'class="inputbox"', $detail->published );

		//clean tourn_info data
		jimport('joomla.filter.filteroutput');	
		JFilterOutput::objectHTMLSafe( $detail, ENT_QUOTES, 'description' );
		
		$this->assignRef('lists',			$lists);
		$this->assignRef('detail',		$detail);
		$this->assignRef('request_url',	$uri->toString());





		parent::display($tpl);
	}


}

?>
