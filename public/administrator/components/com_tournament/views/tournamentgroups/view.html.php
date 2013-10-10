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
case 'edit':
	JToolBarHelper::title( JText::_( 'Tournament Group Manager  - Edit Group' ), 'generic.png' );
	break;
default:
	JToolBarHelper::title( JText::_( 'Tournament Group Manager' ), 'generic.png' );
	break;
}
class TournamentViewTournamentGroups extends JView
{
public function display($tpl = null) {
		$task = JRequest::getVar('task', 'listView');
		
		if(method_exists($this, $task)) {
			$this->$task();
		} else {
			$this->listView();
		}
		
		parent::display($tpl);
		
	}

	
	public function listView()
	{
		JToolBarHelper::addNew('edit','New');
	}
	
	/**
	 * Method to edit tournament Group
	 */
	public function edit()
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_tournament/assets/style.css');
		
		JRequest::setVar('hidemainmenu', 1);
		JToolBarHelper::save("save", "Save");
		JToolBarHelper::cancel();
		
		$this->groups_option_list = array('-1' => JText::_('Select a Parent Group'));
		if (!empty($this->tournament_groups)) {
			foreach($this->tournament_groups as $Group) {
				$this->groups_option_list[$Group->id] = JText::_($group->group);
			}
		}
		
		$this->group_selected_list = FormHelper::getSelectedList($this->groups_option_list, $this->tournament_group_details['parent_group_id']);
		
	}
}