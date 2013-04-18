<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view' );

class TournamentViewTournamentSport extends JView
{
	public function display($tpl = null)
	{
		$task = JRequest::getVar('task', 'list');
		JToolBarHelper::title( JText::_( 'Sports Tournament Manager' ), 'generic.png' );
		switch($task) {
			case 'edit':
				$this->edit();
				break;
			case 'cancelform':
				$this->cancelForm();
				break;
			case 'list':
			default:
				$this->listView();
				break;
    	}
		$document = & JFactory::getDocument();
    	$document->addScript(JURI::root() . DS .'components/com_tournament/assets/common.js');

		parent::display($tpl);
	}

	public function listView()
	{
		JToolBarHelper::addNew('edit');
		JToolBarHelper::preferences('com_tournament', '350');

		$tournament_list =& $this->get('tournament_list', array());

		foreach($tournament_list as $tournament) {
			$tournament->parent_name    = (empty($tournament->parent_name)) ? 'None' : $tournament->parent_name;
			$tournament->sport_name     = ucfirst($tournament->sport_name);

			$tournament->gameplay       = (empty($tournament->jackpot_flag)) ? 'Single' : 'Jackpot';
			$tournament->status         = (empty($tournament->status_flag)) ? 'Unpublished' : 'Published';

			$tournament->buy_in         = (empty($tournament->buy_in)) ? 'Free' : number_format($tournament->buy_in / 100, 2);
			$tournament->entry_fee      = (empty($tournament->entry_fee)) ? 'Free' : number_format($tournament->entry_fee / 100, 2);
		}

		$this->assign('tournament_list', $tournament_list);
	}
	
	public function edit()
  	{
    	JToolBarHelper::save();
    	JToolBarHelper::cancel();
  	}
}