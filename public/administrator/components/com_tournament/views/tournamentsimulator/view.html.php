<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );

class TournamentViewTournamentSimulator extends JView
{
	public function display($tpl = null) 
	{
		$task = JRequest::getVar('task', 'list');
		JToolBarHelper::title( JText::_( 'Simulator' ), 'generic.png' );
		switch ($task) {
			case 'view':
				$this->view();
				break;
			case 'list':
			default:
				$this->main();
				break;
		}

		parent::display($tpl);
	}

	public function main() 
	{
		
		
	}
}