<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class TournamentGroupsController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	private $controllerUrl = 'index.php?option=com_tournament&controller=tournamentgroups';
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
		
		$tournament_group_model =& $this->getModel('TournamentGroups', 'TournamentModel');
		$tournament_group_list = $tournament_group_model->getTournamentGroups();
		
		jimport('joomla.html.pagination');

		$total = count($tournament_group_list);
		$pagination 	= new JPagination($total, $offset, $limit);
		
		
		$view 			=& $this->getView('TournamentGroups', 'html', 'TournamentView');
		$view->setLayout('listview');
		
		$view->assign('tournament_group_list', $tournament_group_list);
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
	* To load the Edit Tournament Label form
	*/
	public function edit()
	{
		// get the id of the record being edited
		$id = JRequest::getVar('id', null);
		
		$formdata	= $this->_getFieldList();
		$fields		= array_keys($this->_getFieldList());
		
		// model 
		$tournament_group_model =& $this->getModel('TournamentGroups', 'TournamentModel');
		if($id){
			// grab the label details
			$tournament_group_details = $tournament_group_model->getTournamentGroupById($id);
		}
		// get all available labels
		$tournament_groups = $tournament_group_model->getTournamentGroups();
		
		
		$view =& $this->getView('TournamentGroups', 'html', 'TournamentView');
		$view->setLayout('edit');
		
		$view->assign('tournament_group_details', $tournament_group_details);
		$view->assign('tournament_groups', $tournament_groups);
		
		$session =& JFactory::getSession();

		$view->display();
	}
	
	private function _getFieldList()
	{
		static $field_list = array(
				
				'group' 								=> -1,
				'descripiption' 						=> -1,
				'parent_group_id'						=> -1
			);
	
		return $field_list;
	}
	
	/**
	 * to Save Tournament Group
	 */
	public function save()
	{
    			
		$id					= JRequest::getVar('id', null);
		$group				= JRequest::getVar('group', null);
		$description		= JRequest::getVar('description', null);
		$parent_label_id	= JRequest::getVar('parent_group_id', null);

		$tournament_group_model =& $this->getModel('TournamentGroups', 'TournamentModel');
		
		if ($id){
			$tournament_group_model->updateTournamentGroup($id, $group, $description, $parent_label_id);
			$this->setRedirect($this->controllerUrl, JText::_('Group updated'));
		}else{
			$tournament_group_model->addTournamentGroup($group, $description, $parent_label_id);
			$this->setRedirect($this->controllerUrl, JText::_('Group Added'));
		}
		
		
	}
}