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
		
		$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');
		$tournament_label_list = $tournament_label_model->getTournamentLabels();
		
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
	* To load the Edit Tournament Label form
	*/
	public function edit()
	{
		// get the id of the record being edited
		$id = JRequest::getVar('id', null);
		
		$formdata	= $this->_getFieldList();
		$fields		= array_keys($this->_getFieldList());
		
		// model 
		$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');
		
		// grab the label details
		$tournament_label_details = $tournament_label_model->getTournamentLabelById($id);
		
		// get all available labels
		$tournament_labels = $tournament_label_model->getTournamentLabels();
		
		
		$view =& $this->getView('TournamentLabels', 'html', 'TournamentView');
		$view->setLayout('edit');
		
		$view->assign('tournament_label_details', $tournament_label_details);
		$view->assign('tournament_labels', $tournament_labels);
		
		$session =& JFactory::getSession();

		$view->display();
	}
	
	private function _getFieldList()
	{
		static $field_list = array(
				
				'label' 								=> -1,
				'descripiption' 						=> -1,
				'parent_tournament_id'					=> -1
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

		$tournament_label_model =& $this->getModel('TournamentLabels', 'TournamentModel');
		
		if ($id){
			$tournament_label_model->updateTournamentLabel($id, $label, $description, $parent_label_id);
			$this->setRedirect($this->controllerUrl, JText::_('Label updated'));
		}else{
			$tournament_label_model->addTournamentLabel($label, $description, $parent_label_id);
			$this->setRedirect($this->controllerUrl, JText::_('Label Added'));
		}
		
		
	}
}