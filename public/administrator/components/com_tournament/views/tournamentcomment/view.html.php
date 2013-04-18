<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//DEVNOTE: import VIEW object class
jimport( 'joomla.application.component.view' );


class TournamentViewTournamentComment extends JView
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
				JToolBarHelper::title( JText::_( 'Tournament Comment' ), 'generic.png' );
				$this->listView();
				break;
		}

		parent::display($tpl);
	}
	
	/**
	 * Method to list comment
	 */
	public function listView()
	{
		$comment_display_list = array();
		foreach ($this->comment_list as $comment) {
			
			if ($comment->private_flag) {
				$tournament_link = '/private/' . $comment->display_identifier;
			} else {
				$tournament_link = '/tournament/details/' . $comment->tournament_id;
			}
			
			$comment_display_list[] = array(
				'id'				=> $comment->id,
				'username'			=> $comment->username,
				'comment'			=> $comment->comment,
				'tournament'		=> '(#' . $comment->tournament_id . ') ' . $comment->tournament_name,
				'tournament_link'	=> $tournament_link,
				'created_date'		=> $comment->created_date == '0000-00-00 00:00:00' ? '' : $comment->created_date,
				'visible'			=> ((time() - strtotime($comment->tournament_end_date))/(60*60) <= 48) ? 'Visible' : 'Hidden',
				'delete_link'		=> 'index.php?option=com_tournament&controller=tournamentcomment&task=deleteComment&id=' . $comment->id
			);
		}
		
		$this->assign('comment_display_list', $comment_display_list);
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