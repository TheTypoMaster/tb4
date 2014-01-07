<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class TournamentLabelsController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	private $controllerUrl = 'index.php?option=com_tournament&controller=tournamentlabels';
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function listView()
	{
		global $mainframe, $option;
		
		$direction = strtoupper($mainframe->getUserStateFromRequest(
			$filter_prefix.'filter_order_Dir',
			'filter_order_Dir',
			'ASC'
		));

		$order = $mainframe->getUserStateFromRequest(
				$filter_prefix.'filter_order',
				'filter_order',
				'v.id'
		);
		
		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$mainframe->getCfg('list_limit')
		);

		$offset = $mainframe->getUserStateFromRequest(
			$filter_prefix.'limitstart',
			'limitstart',
			0
		); 
		
		// get the labels model
		$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');

		// get available labels and groups
		$tournament_label_list = $tournament_label_model->getTournamentLabels();
		$label_group_list = $tournament_label_model->getLabelGroups();
		
		jimport('joomla.html.pagination');

		$total = count($tournament_label_list);
		$pagination 	= new JPagination($total, $offset, $limit);
		
		
		$view 			=& $this->getView('TournamentLabels', 'html', 'TournamentView');
		
		
		$view->setLayout('listview');
		
		$view->assign('tournament_label_list', $tournament_label_list);
		$view->assign('order', $order);
		$view->assign('direction', $direction);
		$view->assign('pagination', $pagination->getListFooter());

		$view->display();
	}
	/**
	 * method to make the cancel button work
	 */
	public function cancel()
	{
		$this->setRedirect($this->controllerUrl);
		return;
	}
	
	
	/**
	* To load the Edit/New Tournament Label form
	*/
	public function edit()
	{
		// get the id of the record being edited
		$id = JRequest::getVar('id', null);
		
		$formdata	= $this->_getFieldList();
		$fields		= array_keys($this->_getFieldList());
		
		// model 
		$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');
		
		$label_group_selected_list = array();
		
		// if were editing an existing label
		if($id){
			// grab the label details were editing
			$tournament_label_details = $tournament_label_model->getTournamentLabelById($id);
			
			// get all groups label is linked to
			$label_group_selected_list = $tournament_label_model->getLabelGroupsByLabelId($id);
		}
			
		// get all available labels
		$tournament_labels = $tournament_label_model->getTournamentLabels();
		
		// get all available groups
		$label_groups = $tournament_label_model->getLabelGroups();
		
		//set the view
		$view =& $this->getView('TournamentLabels', 'html', 'TournamentView');
		$view->setLayout('edit');
		
		//assign model data to the view
		$view->assign('tournament_label_details', $tournament_label_details);
		$view->assign('tournament_labels', $tournament_labels);
		$view->assign('label_groups', $label_groups);
		$view->assign('label_groups_selected_list', $label_group_selected_list);
		
		$session =& JFactory::getSession();

		// display the view
		$view->display();
	}
	
	private function _getFieldList()
	{
		static $field_list = array(
				
				'label' 								=> -1,
				'descripiption' 						=> -1,
				'parent_label_id'						=> -1
			);
	
		return $field_list;
	}
	
	/**
	 * to Save Tournament Label
	 */
	public function save()
	{
    			
		$id					= JRequest::getVar('id', null);
		$label				= JRequest::getVar('label', null);
		$description		= JRequest::getVar('description', null);
		$parent_label_id	= JRequest::getVar('parent_label_id', null);
		$labelGroups 		= JRequest::getVar('label_group_id', '','array');
		
		// Get the labels model
		$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');
		
		if ($id){
			$tournament_label_model->updateTournamentLabel($id, $label, $description, $parent_label_id);
			$updateText = "Updated";
		}else{
			$id = $tournament_label_model->addTournamentLabel($label, $description, $parent_label_id);
			$updateText = "Added";
		}
		
		/*
		 * Label grouping pivot tables stuff
		*/

		// Remove existing groups for label
		$tournament_label_model->deleteLabelGroupsByLabelId($id);
			
		// Add new groups for label
		foreach($labelGroups as $groupId){
			$tournament_label_model->addLabelGroupToLabel($id, $groupId);
		}
	
		$this->setRedirect($this->controllerUrl, JText::_('Label/Groups '. $updateText));
	}
	
	/**
	 * to Delete a Tournament Label and all it's associations
	 */
	public function delete()
	{
		$id	= JRequest::getVar('id', null);
		
		if($id){
			// get the labels model
			$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');
		
			// remove labels from tournaments
			$tournament_label_model->deleteTournamentLabelsByLabelId($id);
			
			// remove label from groups
			$tournament_label_model->deleteLabelGroupsByLabelId($id);
			
			// remove parent label ref's from other labels
			$tournament_label_model->removeParentLabelRefs($id);	
			
			// remove the actual label
			$tournament_label_model->deleteLabelsByLabelId($id);
			
			$this->setRedirect($this->controllerUrl, JText::_('Label deleted, removed from Tournaments, Groups and parent labels!'));
		}else{
			$this->setRedirect($this->controllerUrl, JText::_('No Label ID passed in to delete!'));
		}
	}
	
}