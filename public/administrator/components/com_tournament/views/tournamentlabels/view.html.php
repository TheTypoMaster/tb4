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
	JToolBarHelper::title( JText::_( 'Tournament Label Manager  - Edit Label' ), 'generic.png' );
	break;
default:
	JToolBarHelper::title( JText::_( 'Tournament Label Manager' ), 'generic.png' );
	break;
}
class TournamentViewTournamentLabels extends JView
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
	 * Method to edit tournament label
	 */
	public function edit()
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet('components/com_tournament/assets/style.css');
		
		JRequest::setVar('hidemainmenu', 1);
		JToolBarHelper::save("save", "Save");
		JToolBarHelper::cancel();
		
		$this->labels_option_list = array('-1' => JText::_('Select a Parent Tournament'));
		if (!empty($this->tournament_labels)) {
			foreach($this->tournament_labels as $label) {
				$this->labels_option_list[$label->id] = JText::_($label->label);
			}
		}
		
		$this->label_selected_list = FormHelper::getSelectedList($this->labels_option_list, $this->tournament_label_details['parent_label_id']);
		
	}
}