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
case 'editmeetingvenue':
	JToolBarHelper::title( JText::_( 'Racing Meeting Venue Manager  - Edit Venue' ), 'generic.png' );
	break;
default:
	JToolBarHelper::title( JText::_( 'Racing Meeting Venue Manager' ), 'generic.png' );
	break;
}
class TournamentViewTournamentMeetingVenue extends JView
{
	public function display($tpl = null)
	{
		$task = JRequest::getVar('task');
		switch($task) {
			case 'editmeetingvenue':
				$this->editMeetingVenue();
				break;
			case 'list':
			default:
				break;
		}

		parent::display($tpl);
	}

	/**
	 * Method to edit odds
	 */
	public function editMeetingVenue()
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_tournament/assets/style.css');
		
		JRequest::setVar('hidemainmenu', 1);
		JToolBarHelper::save("saveMeetingVenue", "Save");
		JToolBarHelper::cancel();
	}
}