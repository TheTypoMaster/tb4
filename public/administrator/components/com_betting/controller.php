<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller' );

class BettingController extends JController {
	
    public function display()
    {
		JRequest::setVar('task', 'list');
		global $mainframe, $option;
		
		$bet_model					=& $this->getModel('Bet', 'BettingModel');
		$bet_selection_model		=& $this->getModel('BetSelection', 'BettingModel');
		$bet_result_status_model	=& $this->getModel('BetResultStatus', 'BettingModel');
		
		$filter_keyword		= $mainframe->getUserStateFromRequest($option.'filter_bets_keyword', 'filter_bets_keyword');
		$filter_result_type	= $mainframe->getUserStateFromRequest($option.'filter_bets_result_type', 'filter_bets_result_type');
		$filter_from_date	= $mainframe->getUserStateFromRequest($option.'filter_bets_from_date', 'filter_bets_from_date');
		$filter_to_date		= $mainframe->getUserStateFromRequest($option.'filter_bets_to_date', 'filter_bets_to_date');
		$filter_from_amount	= $mainframe->getUserStateFromRequest($option.'filter_bets_from_amount', 'filter_bets_from_amount');
		$filter_to_amount	= $mainframe->getUserStateFromRequest($option.'filter_bets_from_amount', 'filter_bets_to_amount');
		
		$lists = array(
			'keyword'		=> $filter_keyword,
			'result_type'	=> $filter_result_type,
			'from_date'		=> $filter_from_date,
			'to_date'		=> $filter_to_date,
			'from_amount'	=> $filter_from_amount,
			'to_amount'		=> $filter_to_amount,
		);
		
		$offset = $mainframe->getUserStateFromRequest(
			JRequest::getVar('limitstart', 0, '', 'int'),
			'limitstart',
			0
		);
		
		$filter	= array(
			'keyword'		=> $filter_keyword,
			'result_type'	=> $filter_result_type,
			'from_time'		=> $filter_from_date ? strtotime($filter_from_date) : (time() - 24 * 60 * 60),
			'to_time'		=> $filter_to_date ? (strtotime($filter_to_date) + 24 * 60 * 60) : time(),
			'from_amount'	=> $filter_from_amount,
			'to_amount'		=> $filter_to_amount,
		);
		
		$limit = $mainframe->getCfg('list_limit');
		$bet_list = $bet_model->getBetFilterList($filter, 'b.id DESC', 'ASC', $limit, $offset);
		
		jimport('joomla.html.pagination');
		$total = $bet_model->getBetFilterCount($filter);
		$pagination = new JPagination($total, $offset, $limit);
		
    	$view =& $this->getView('Betting', 'html', 'BettingView');
    
		$view->assignRef('lists', $lists);
		$view->assignRef('bet_list', $bet_list);
		$view->assign('pagination', $pagination->getPagesLinks());
		
		$bet_selection_model =& $this->getModel('BetSelection', 'BettingModel');
		$view->setModel($bet_selection_model);
		
		$selection_price_model =& $this->getModel('SelectionResult', 'TournamentModel');
		$view->setModel($selection_price_model);
		
    	$view->display();
    }
}
?>