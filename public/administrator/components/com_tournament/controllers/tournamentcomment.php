<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class TournamentCommentController extends JController
{
	/**
	 * Controller URL is to reuse the same path to all the places nessessary
	 */
	private $controllerUrl = 'index.php?option=com_tournament&controller=tournamentcomment';
	/**
	 * Display the list of racing tournaments
	 *
	 * @return void
	 */
	public function listView()
	{
		global $mainframe, $option;
		$tournament_id		= JRequest::getVar('tournamentId', null);
		$username			= JRequest::getVar('username', null);
		$visible			= JRequest::getVar('visible', null);

		$comment_model 		=& $this->getModel('TournamentComment', 'TournamentModel');
		
		$filter_prefix = 'tournamentcomment';

		$order = $mainframe->getUserStateFromRequest(
			$filter_prefix.'filter_order',
			'filter_order',
			'tc.id'
		);

		$direction = strtoupper($mainframe->getUserStateFromRequest(
			$filter_prefix.'filter_order_Dir',
			'filter_order_Dir',
			'DESC'
		));

		$limit = $mainframe->getUserStateFromRequest(
			$filter_prefix. 'list.limit',
			'limit',
			$mainframe->getCfg('list_limit')
		);

		$offset = $mainframe->getUserStateFromRequest(
			$filter_prefix.'limitstart',
			'limitstart',
			0
		);
		
		$params = array(
			'visible'	=> $visible,
			'order'		=> $order,
			'direction'	=> $direction,
			'limit'		=> $limit,
			'offset'	=> $offset
		);
		
		$comment_list	= $comment_model->getTournamentCommentListByTournamentIDAndUsername($tournament_id, $username, $params);
		
		jimport('joomla.html.pagination');

		$total = $comment_model->getTotalTournamentCommentCountByTournamentIDAndUsername($tournament_id, $username, $params);
		
		$pagination 	= new JPagination($total, $offset, $limit);
		$view 			=& $this->getView('TournamentComment', 'html', 'TournamentView');

		$view->assign('comment_list', $comment_list);
		$view->assign('tournament_id', $tournament_id);
		$view->assign('username', $username);
		$view->assign('visible', $visible);
		
		$view->assign('order', $order);
		$view->assign('direction', $direction);
		$view->assign('pagination', $pagination->getPagesLinks());

		$view->display();
	}
	/**
	 * method to delete comment
	 */
	public function deleteComment()
	{
		$id	= JRequest::getVar('id', null);
		
		if(empty($id)) {
			$this->setRedirect($this->controllerUrl, JText::_('Comment Id is empty'), 'error');
			return;
		}
		
		$comment_model	=& $this->getModel('TournamentComment', 'TournamentModel');
		$comment		= $comment_model->getTournamentComment($id);
		if(empty($comment)) {
			$this->setRedirect($this->controllerUrl, JText::_('Comment not found'), 'error');
			return;
		}
		
		if (!$comment_model->delete($comment->id)) {
			$this->setRedirect($this->controllerUrl, JText::_('Could not delete the comment'), 'error');
			return;
		}
		
		$this->setRedirect($this->controllerUrl, JText::_('Comment deleted'));
		return;
	}
}