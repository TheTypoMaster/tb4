<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );


/**
 * Adding the Page title based on the tasks
 */
switch( JRequest::getVar('task') )
{
case 'editOdds':
	JToolBarHelper::title( JText::_( 'Sports Odds Manager - Edit Odds' ), 'generic.png' );
	break;
case 'resultMatch':
	JToolBarHelper::title( JText::_( 'Sports Odds Manager - Result Match' ), 'generic.png' );
	break;
default:
	JToolBarHelper::title( JText::_( 'Sports Odds Manager' ), 'generic.png' );
	break;
}
class TournamentViewTournamentSportOffer extends JView
{
	public function display($tpl = null)
	{
		$task = JRequest::getVar('task');
		switch($task) {
			case 'editOdds':
				$this->editOdds();
				break;
			case 'resultMatch':
				$this->resultMatch($tpl);
				break;
			case 'view':
				$this->view();
				break;
			case 'list':
			default:
				$this->listView();
				break;
		}

		// page setup
		$document = & JFactory::getDocument();
		$document->addScript(JURI::root() . DS .'components/com_tournament/assets/common.js');

		parent::display($tpl);
	}

	public function listView()
	{


	}
	/**
	 * Method to edit odds
	 */
	public function editOdds()
	{
		JToolBarHelper::save("saveOdds", "Save");
		JToolBarHelper::save("abandonMatch", "Abandon Match");
		JToolBarHelper::cancel();
	}

	/**
	 * Method to result match offers
	 */
	public function resultMatch()
	{

		if(!$this->match_is_resulted){
			JToolBarHelper::save("saveResult", "Save and Result");
			JToolBarHelper::save("abandonMatch", "Abandon Match");
		}
		JToolBarHelper::cancel();
	}
}