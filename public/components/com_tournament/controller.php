<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
require_once JPATH_SITE . DS . 'components' . DS . 'com_betting' . DS . 'controller.php';

/**
 * Tournament Controller
 */
class TournamentController extends BettingController
{
	protected $extra_authenticate	= null;
	protected $extra_squeezebox		= null;
	/**
	 * Constructor which will check authentication on required tasks
	 */
	public function __construct()
	{
		$authenticate = array(
			'tournamenthistory',
			'privatetournamenthistory',
			'confirmticket',
			'saveticket',
			'privatetournament',
		);

		if ($this->extra_authenticate != null && is_array($this->extra_authenticate)) {
			$authenticate = array_merge($authenticate, $this->extra_authenticate);
		}

		$squeezebox = array(
			'confirmticket',
			'privatetournament',
		);

		if ($this->extra_squeezebox != null && is_array($this->extra_squeezebox)) {
			$squeezebox = array_merge($squeezebox, $this->extra_squeezebox);
		}

		$user =& JFactory::getUser();
		$task = JRequest::getVar('task', 'display');

		parent::__construct(false);

		if ($user->guest && in_array($task, $authenticate)) {

			if (in_array($task, $squeezebox)) {
				JRequest::setVar('task', 'squeezeboxredirect');
				$this->squeezeboxRedirect('/user/register');
				exit;
			}

      		$msg = JText::_("You need to login to access this part of the site.");
			$this->setRedirect('/user/register', $msg, 'error');
			$this->redirect();
		}
	}
	/**
	 * Display the homepage
	 *
	 * @return void
	 */
	public function display()
	{
		$user =& JFactory::getUser();

		// this reuses the TournamentRacing view for now until we have other tournaments
		$view =& $this->getView('TournamentRacing', 'html', 'TournamentView');

		//get banner script from tournament param
		$config =& JComponentHelper::getParams( 'com_tournament' );
		$view->assign('left_banner', $config->get('left_banner'));
		$view->assign('right_banner', $config->get('right_banner'));

		$view->display();
	}

	/**
	 * Display create private tournaments form
	 *
	 * @return void
	 */
	public function privateTournament()
	{
		$user =& JFactory::getUser();

		$sport_id			= JRequest::getVar('sport_id', null);
		$competition_id		= JRequest::getVar('competition_id', null);
		$event_group_id		= JRequest::getVar('event_group_id', null);
		$from_tournament_id	= JRequest::getVar('from_tournament_id', null);

		//set up sports
		$sport_model		=& $this->getModel('TournamentSport', 'TournamentModel');
		$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
		$sport_event_model	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
		
		$view =& $this->getView('Tournament', 'html', 'TournamentView');

		$session	=& JFactory::getSession();
		$formerrors	= array();
		if ($sessFormData	= $session->get('sessFormData', null, 'privatetournament')) {
			if ($formerrors	= $session->get('sessFormErrors', null, 'privatetournament')) {
				$session->clear('sessFormErrors', 'privatetournament');
			}

			foreach ($sessFormData as $k => $data) {
				$formdata[$k] = stripslashes($data);
			}

			$sport_id		= $formdata['sport_id'];
			$competition_id	= $formdata['competition_id'];
			$event_group_id	= $formdata['event_group_id'];

			$session->clear('sessFormData', 'privatetournament');
		}
		$view->assignRef('formerrors', $formerrors);
		
		$sport_list			= $sport_event_model->getActiveTournamentSportList(0);
		if (is_null($sport_id)) {
			$sport_id = $sport_list[0]->id;
		}
		
		$competition_list	= $competition_model->getActiveTournamentCompetitionListBySportID($sport_id, 0);
		if (is_null($competition_id)) {
			$competition_id = $competition_list[0]->id;
		}

		$event_group_model	=& $this->getModel('TournamentEventGroup', 'TournamentModel');

		//set up prize formats
		$prize_format_model	=& $this->getModel('TournamentPrizeFormat', 'TournamentModel');
		$prize_format_list	= $prize_format_model->getTournamentPrizeFormats();

		//set up buy-ins
		$buyin_model		=& $this->getModel('TournamentBuyIn', 'TournamentModel');
		$buyin_list			= $buyin_model->getTournamentBuyInList();

		//get pre-population field
		$formdata = array(
			'sport_id' 				=> $sport_id,
			'competition_id'		=> $competition_id,
			'event_group_id'		=> $event_group_id,
			'from_tournament_id'	=> $from_tournament_id,
		);

		$event_group_list	= $event_group_model->getActiveTournamentEventGroupListByCompetitonID($competition_id);
		
		$view->setModel($sport_model);
		$view->setModel($competition_model);
		$view->setModel($event_group_model);
		$view->setModel($prize_format_model);
		$view->setModel($buyin_model);

		$view->assignRef('sport_list', $sport_list);
		$view->assignRef('competition_list', $competition_list);
		$view->assignRef('event_group_list', $event_group_list);
		$view->assignRef('prize_format_list', $prize_format_list);
		$view->assignRef('buyin_list', $buyin_list);
		$view->assignRef('formdata', $formdata);
		$view->assignRef('from_tournament_id', $from_tournament_id);

		$view->display();
	}

	/**
	 * Register private tournaments
	 *
	 * @return void
	 */
	public function registerPrivateTournament()
	{
		$user =& JFactory::getUser();

		if ($user->guest) {
			JRequest::setVar('task', 'squeezeboxredirect');
			$this->squeezeboxRedirect('/user/register');
			return;
		}

		$session =& JFactory::getSession();

		// begin the painstaking task of validating a bet
		$sport_id			= JRequest::getVar('sport_id', null);
		$competition_id		= JRequest::getVar('competition_id', null);
		$event_group_id		= JRequest::getVar('event_group_id', null);
		$buyin_id			= JRequest::getVar('buyin_id', null);
		$prize_format_id	= JRequest::getVar('prize_format_id', null);
		$tournament_name	= JRequest::getVar('tournament_name', null);
		$required_password	= JRequest::getVar('required_password', 0);
		$password			= JRequest::getVar('password', null);
		$from_tournament_id	= JRequest::getVar('from_tournament_id', null);

		$error 			= array();
		$redirect_link	= '/index.php?option=com_tournament&task=privatetournament&format=raw';

		$tournament_model			=& $this->getModel('Tournament', 'TournamentModel');
		$tournament_private_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
		$buyin_model				=& $this->getModel('TournamentBuyIn', 'TournamentModel');
		$prize_format_model			=& $this->getModel('TournamentPrizeFormat', 'TournamentModel');
		$sport_model				=& $this->getModel('TournamentSport', 'TournamentModel');

		//validations
		$is_racing	= ($sport_id == 'racing');
		if ($is_racing) {
			if (!in_array($competition_id, $sport_model->excludeSports)) {
				$error['competition_id'] = JText::_('Invalid competition');
			}
			$meeting_model		=& $this->getModel('Meeting', 'TournamentModel');
			$meeting_type_model	=& $this->getModel('MeetingType', 'TournamentModel');
			$racing_model		=& $this->getModel('TournamentRacing', 'TournamentModel');

			$meeting_type_id = $meeting_type_model->getMeetingTypeIDByName($competition_id);
			$event_list		= $meeting_model->getActiveMeetingListByMeetingTypeID($meeting_type_id);

			if (!isset($event_list[$event_id])) {
				$error['event_id'] = JText::_('Invalid event');
			}
		} else {
			$tournament_sport_event		=& $this->getModel('TournamentSportEvent', 'TournamentModel');
			$competition_model			=& $this->getModel('TournamentCompetition', 'TournamentModel');
			$event_group_model			=& $this->getModel('TournamentEventGroup', 'TournamentModel');

			if (empty($sport_id)) {
				$error['sport_id'] = JText::_('Please select a sport');
			} else {
				$sport = $sport_model->getTournamentSport($sport_id);

				if (empty($sport)) {
					$error['sport_id'] = JText::_('Invalid sport');
				}
			}

			if (empty($competition_id)) {
				$error['competition_id'] = JText::_('Please select a competition');
			} else {
				$competition = $competition_model->getTournamentCompetition($competition_id);

				if (empty($competition)) {
					$error['competition_id'] = JText::_('Invalid competition');
				}
			}

			if (empty($event_group_id)) {
				$error['event_group_id'] = JText::_('Please select an event');
			} else {
				$event_group_list = $event_group_model->getActiveTournamentEventGroupListByCompetitonID($competition->id);
				if (!isset($event_group_list[$event_group_id])) {
					$error['event_group_id'] = JText::_('Invalid event');
				} else {
					$event_group = $event_group_model->getEventGroup($event_group_id);
				}
			}
		}

		if (empty($buyin_id)) {
			$error['buyin_id'] = JText::_('Please select a buy in option');
		} else {
			$buyin = $buyin_model->getTournamentBuyIn($buyin_id);

			if (empty($buyin) || ($buyin->buy_in < 2 && $buyin->buy_in != 0) || $buyin->buyin > 100) {
				$error['buyin_id'] = JText::_('Invalid buy-in option');
			}
		}

		if (empty($prize_format_id)) {
			$error['prize_format_id'] = JText::_('Please select a prize format');
		} else {
			$prize_format = $prize_format_model->getTournamentPrizeFormat($prize_format_id);

			if (empty($prize_format)) {
				$error['prize_format_id'] = JText::_('Invalid prize format');
			} else if (is_object($buyin) && 0 == $buyin->buy_in && $prize_format->keyword != 'all') {
				$error['prize_format_id'] = JText::_('Invalid Option');
			}
		}

		$tournament_name_len = strlen($tournament_name);
		if (0 == $tournament_name_len) {
			$error['tournament_name'] = JText::_('Please enter the tournament name');
		} else if ($tournament_name_len < 5 || $tournament_name_len > 75) {
			$error['tournament_name'] = 'Tournament name should between 5-75 characters long';
		}

		$password_len = strlen($password);
		if ($required_password && 0 == $password_len) {
			$error['password'] = JText::_('Please enter the tournament password');
		} else if ($required_password && $password_len < 5) {
			$error['password'] = JText::_('Passwords should be at least 5 characters long');
		}

		if (!$required_password && $password) {
			$error['required_password'] = JText::_('Please tick the box to enable password');
		}

		if ($error) {
			$session->set('sessFormErrors', $error, 'privatetournament');
			$session->set('sessFormData', $_POST, 'privatetournament');
			$this->setRedirect($redirect_link);
			return false;
		}

		$tournament_sport_id			= $sport_id;
		$tournament_event_group_model	=& $this->getModel('TournamentEventGroup', 'TournamentModel');
		$tournament_event_group			= $tournament_event_group_model->getEventGroupFirstAndLastEventTimeByEventGroupID($event_group_id);
		
		if (!empty($tournament_event_group->first_match_time)) {
			$start_date				= $tournament_event_group->first_match_time;
			$end_date				= $tournament_event_group->last_match_time;
		} else {
			$event_group	= $tournament_event_group_model->getEventGroup($event_group_id);
			$start_date	= $end_date = $event_group->start_date;
		}

		$description = $user->username . ' created this tournament on ' . date('d/m/y') . '. The total entry fee is $' . number_format($buyin->buy_in+$buyin->entry_fee, 2) . '. The prize format is "' . $prize_format->name . '".';

		//save tournament
		$params = array(
			'tournament_sport_id'					=> $tournament_sport_id,
			'parent_tournament_id'					=> -1,
			'event_group_id'						=> $event_group->id,
			'name'									=> $tournament_name,
			'description'							=> $description,
			'start_currency'						=> 100000,
			'start_date'							=> $start_date,
			'end_date'								=> $end_date,
			'jackpot_flag'							=> 0,
			'buy_in'								=> $buyin->buy_in * 100,
			'entry_fee'								=> $buyin->entry_fee * 100,
			'minimum_prize_pool'					=> 0,
			'paid_flag'								=> 0,
			'auto_create_flag'						=> 0,
			'cancelled_flag'						=> 0,
			'cancelled_reason'						=> '',
			'status_flag'							=> 1,
			'private_flag'							=> 1,
			'closed_betting_on_first_match_flag'	=> 0,
			'betting_closed_date'					=> $end_date,
			'reinvest_winnings_flag'				=> 1,
		);

		$tournament_id = $tournament_model->store($params);
		if (empty($private_tournament_id)) {
			$error['general'] = JText::_("Sorry, we were unable to create your private tournament.\nPlease try again later.");
			$session->set('sessFormErrors', $error, 'privatetournament');
			$this->setRedirect($redirect_link);
		}

		$private_params = array(
			'tournament_id'					=> $tournament_id ,
			'tournament_prize_format_id'	=> $prize_format->id,
			'user_id'						=> $user->id,
			'display_identifier'			=> $this->_generatePrivateTournamentCode($tournament_private_model),
			'password'						=> $password,
		);
		$private_tournament_id = $tournament_private_model->store($private_params);

		if (empty($private_tournament_id)) {
			$error['general'] = JText::_("Sorry, we were unable to create your private tournament. Please try again later.");
			$session->set('sessFormErrors', $error, 'privatetournament');
			$this->setRedirect($redirect_link);
		}

		$success_redirect_link = '/index.php?option=com_tournament&task=squeezeboxredirect&format=raw&id=' . $tournament_id;
		$this->setMessage(JText::_('Success - Your private tournament has been created!'));
		$this->setRedirect($success_redirect_link);
		return true;
	}

	public function squeezeboxRedirect($url=null)
	{
		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$tournament_id = JRequest::getVar('id', null);
		if (empty($url) && !empty($tournament_id)) {
			$url = '/tournament/details/' . $tournament_id;
		}
		if (empty($url)) {
			$url = '/';
		}
		$view->redirect_url = $url;
		$view->display();
	}

	/**
	 * Search private tournament
	 *
	 * return void
	 */
	public function searchPrivateTournament()
	{
		$tournament_code = JRequest::getVar('tournament_code', null);
		if (empty($tournament_code)) {
			$this->setRedirect('index.php', JText::_('Please enter a tournament code.'), 'error');
		} else {
			$private_tournament_model =& $this->getModel('TournamentPrivate', 'TournamentModel');
			$private_tournament = $private_tournament_model->getTournamentPrivateByIdentifier($tournament_code);
			if (empty($private_tournament)) {
				$this->setRedirect('index.php', JText::_('Invalid tournament code.'), 'error');
			} else {
				$this->setRedirect('/private/'. $tournament_code);
			}
		}

		$this->redirect();
	}

	/**
	 * List tournament history
	 *
	 * return void
	 */
	public function tournamentHistory()
	{
		$user =& JFactory::getUser();
		if (!is_object($user->account_balance) || !is_object($user->tournament_dollars)) {
			$this->redirect('/');
		}
		
		global $mainframe, $option;

		// display list
		$tournament_model	=& $this->getModel('Tournament', 'TournamentModel');
		$ticket_model 		=& $this->getModel('TournamentTicket', 'TournamentModel');

		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
		$mainframe->getCfg('list_limit')
		);

		$offset = $mainframe->getUserStateFromRequest(
		JRequest::getVar('limitstart', 0, '', 'int'),
			'limitstart',
		0
		);

		$tournament_list = $ticket_model->getUserTournamentList($user->id, 'tk.id', 'DESC', $limit, $offset);
		
		foreach ($tournament_list as $tournament) {
			//set bet open
			$tournament->bet_open		= strtotime($tournament->end_date) > time();
			//populate bettabucks
			$tournament->betta_bucks	= $ticket_model->getAvailableTicketCurrency($tournament->id, $user->id);
			//get leaderboard rank

			$leaderboard_model				= JModel::getInstance('TournamentLeaderboard', 'TournamentModel');
			$leaderboard					= $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);
			$tournament->leaderboard_rank	= $leaderboard->rank;

			$tournament->prize				= null;
			$tournament->ticket_awarded		= null;

			$tournament_sport_model			= JModel::getInstance('TournamentSport', 'TournamentModel');
			$tournament->type				= (in_array($tournament->sport_name, $tournament_sport_model->excludeSports) ? 'racing' : 'sports');

			$transaction_record				= null;
			$parent_tournament				= null;
			if ($tournament->result_transaction_id) {
				if ($tournament->jackpot_flag && !empty($tournament->parent_tournament_id) && -1 != $tournament->parent_tournament_id) {
					$transaction_record	= $user->tournament_dollars->getTournamentTransaction($tournament->result_transaction_id);
					$parent_tournament	= $tournament_model->getTournament($tournament->parent_tournament_id);
				} else {
					$transaction_record	=  $user->account_balance->getAccountTransaction($tournament->result_transaction_id);
				}
			}
			if ($transaction_record && $transaction_record->amount > 0) {
				$tournament->prize = $transaction_record->amount;

				if ($tournament->jackpot_flag && !empty($parent_tournament) && -1 != $tournament->parent_tournament_id) {
					$ticket_cost = $parent_tournament->entry_fee + $parent_tournament->buy_in;

					if ($tournament->prize > $ticket_cost) {
						$tournament->ticket_awarded	= $parent_tournament->id;
						$tournament->prize			= $tournament->prize - $ticket_cost;
					}
				}
			}
		}

		jimport('joomla.html.pagination');
		$total = $ticket_model->getUserTournamentCount($user->id);

		$pagination = new JPagination($total, $offset, $limit);

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->assignRef('tournament_list', $tournament_list);
		$view->assign('pagination', $pagination->getPagesLinks());

		$sport_model =& $this->getModel('TournamentSport', 'TournamentModel');
		$view->setModel($sport_model);

		$view->display();
	}

	/**
	 * List private tournament history
	 *
	 * return void
	 */
	public function privateTournamentHistory()
	{
		$user =& JFactory::getUser();
		if (!is_object($user->account_balance) || !is_object($user->tournament_dollars)) {
			$this->redirect('/');
		}

		global $mainframe, $option;

		// display list
		$tournament_model			=& $this->getModel('Tournament', 'TournamentModel');
		$tournament_private_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
		$ticket_model				=& $this->getModel('TournamentTicket', 'TournamentModel');

		$limit = $mainframe->getUserStateFromRequest(
			'global.list.limit',
			'limit',
			$mainframe->getCfg('list_limit')
		);

		$offset = $mainframe->getUserStateFromRequest(
		JRequest::getVar('limitstart', 0, '', 'int'),
				'limitstart',
		0
		);

		$private_tournament_list =
		$tournament_list = $tournament_private_model->getUserTournamentPrivateList($user->id, 'p.id', 'DESC', $limit, $offset);

		foreach ($tournament_list as $tournament) {
			$tournament->prize_pool	= $tournament_model->calculateTournamentPrizePool($tournament->id);
			$tournament->entrants	= $ticket_model->countTournamentEntrants($tournament->id);

			$tournament_sport_model	= JModel::getInstance('TournamentSport', 'TournamentModel');
			$tournament->type		= (in_array($tournament->sport_name, $tournament_sport_model->excludeSports) ? 'racing' : 'sports');
		}

		jimport('joomla.html.pagination');
		$total = $tournament_private_model->getUserTournamentPrivateCount($user->id);

		$pagination = new JPagination($total, $offset, $limit);

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->assignRef('tournament_list', $tournament_list);
		$view->assign('pagination', $pagination->getPagesLinks());

		$sport_model =& $this->getModel('TournamentSport', 'TournamentModel');
		$view->setModel($sport_model);

		$view->display();
	}

	/**
	 * Ajax call for different purposes
	 * Used for Ajax
	 *
	 * return void
	 */
	public function ajaxcall()
	{
		$type = JRequest::getVar('type');

		$view =& $this->getView('Tournament', 'html', 'TournamentView');

		switch ($type) {
			case 'sport':
				$competition_id		= JRequest::getVar('competition_id');
				$sport_event_model		=& $this->getModel('TournamentSportEvent', 'TournamentModel');
				$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');

				$sport_list			= $sport_event_model->getActiveTournamentSportList(0);
				$competition		= $competition_model->getTournamentCompetition($competition_id);

				$view->assign('selected_sport_id', $competition->tournament_sport_id);
				$view->assign('sport_list', $sport_list);

				break;
			case 'competition':
				$sport_id 			= JRequest::getVar('sport_id');
				$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
				$sport_model		=& $this->getModel('TournamentSport', 'TournamentModel');

				$competition_list = $competition_model->getActiveTournamentCompetitionListBySportID($sport_id, 0);

				$view->assign('sport_id', $sport_id);
				$view->assign('competition_list', $competition_list);

				break;
			case 'eventgroup':
				$sport_model		=& $this->getModel('TournamentSport', 'TournamentModel');
				$event_group_model	=& $this->getModel('TournamentEventGroup', 'TournamentModel');
				$competition_id	= JRequest::getVar('competition_id');

				$event_group_list	= $event_group_model->getActiveTournamentEventGroupListByCompetitonID($competition_id);

				$view->assign('competition_id', $competition_id);
				$view->assign('event_group_list', $event_group_list);
				break;
		}

		$view->assign('type', $type);
		$view->display();
	}

	/**
	 * Display the tournament information page
	 *
	 * @return void
	 */
	public function tournamentDetails()
	{
		$id			= JRequest::getVar('id', null);
		$identifier	= JRequest::getVar('identifier', null);
		/**
		 * check private tournament with identifier
		 */
		if (!empty($identifier)) {
			$private_tournament_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
			$private_tournament			= $private_tournament_model->getTournamentPrivateByIdentifier($identifier);
			$id = $private_tournament->tournament_id;
		}
		if (is_null($id)) {
			$this->setRedirect('index.php', JText::_('No tournament selected'));
			return;
		}

		$sport_model 			=& $this->getModel('TournamentSport', 'TournamentModel');
		$is_racing_tournament 	= $sport_model->isRacingByTournamentId($id);

		if ($is_racing_tournament) {
			$racing_model	=& $this->getModel('TournamentRacing', 'TournamentModel');
			$tournament 	= $racing_model->getTournamentRacingByTournamentID($id);
		} else {
			$sport_event_model 	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
			$tournament 		= $sport_event_model->getTournamentSportsByTournamentID($id);
		}

		if (is_null($tournament)) {
			$this->setRedirect('index.php', JText::_('Tournament not found'));
			return;
		}
		$user =& JFactory::getUser();

		$private_tournament_link	= null;
		$private_tournament_url		= null;
		$shorten_url				= null;
		if (empty($tournament->private_flag)) {
			$sport_id		= $tournament->tournament_sport_id;
			$competition_id	= $tournament->tournament_competition_id;
			if ($is_racing_tournament) {
				$event_group_id	= $tournament->meeting_id;
			} else {
				$event_group_id	= $tournament->event_group_id;
			}

			if (!$user->guest) {
				$private_tournament_link = '/index.php?option=com_tournament&task=privatetournament&format=raw&sport_id=' . $sport_id . '&competition_id=' . $competition_id . '&event_group_id=' . $event_group_id . '&from_tournament_id=' . $tournament->id;
			}
		} else {
			/**
			 * For Private tournament
			 * Only accessible with the identifier
			 */
			if (is_null($identifier)) {
				$private_tournament_model	=& $this->getModel('TournamentPrivate', 'TournamentModel');
				$private_tournament			= $private_tournament_model->getTournamentPrivateByTournamentID($id);
				$ticket_model				=& $this->getModel('TournamentTicket', 'TournamentModel');
				$ticket						= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $id);

				if ($user->id < 1 || ($user->id != $private_tournament->user_id && $ticket->id < 1 )) {
					$this->setRedirect('index.php', JText::_('You need be invited to get in a private tournament'));
					return;
				}
			}

			$private_tournament_prize_format_model	=& $this->getModel('TournamentPrizeFormat', 'TournamentModel');
			$private_tournament_prize_format		= $private_tournament_prize_format_model->getTournamentPrizeFormat($private_tournament->tournament_prize_format_id);

			$tournament->prize_format_id = $private_tournament->tournament_prize_format_id;

			if ($user->id == $private_tournament->user_id) {
				$tournament_owner 		= $user->id;
				$private_tournament_url = JURI::base()."private/".$private_tournament->display_identifier;
				$shorten_url 			= $this->shortenUrl($private_tournament_url);
			}
		}

		$ticket_model	=& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket			= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		if ($is_racing_tournament) {
			$race_model 				=& $this->getModel('Race', 'TournamentModel');
			$next_match 				= $race_model->getNextRaceNumberByTournamentID($tournament->id);
			$tournament_races 			= $race_model->getRaceListByTournamentID($tournament->id);
			$tournament_races_completed = false;
			$tournament_completed 		=& $tournament_races_completed;

			if (!empty($tournament_races) && is_null($next_match)) {
				$next_match = '';
				//checking if there is any unfinished race
				$tournament_races_completed = true;
				foreach ($tournament_races as $tournament_race) {
					if (($tournament_race->paid_flag == 0) && ($tournament_race->status != 'Abandoned')) {
						$tournament_races_completed = false;
						break;
					}
				}
			}
		} else {
			$event_model			=& $this->getModel('Event', 'TournamentModel');
			$next_match				= $event_model->getNextEventByEventGroupID($tournament->event_group_id);
			$tournament_completed 	= $tournament->paid_flag;
		}

		$player_list        = $ticket_model->getTournamentEntrantList($tournament->id);
		$leaderboard        = array();
		$leaderboard_rank   = null;

		$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');

		if (strtotime($tournament->start_date) < time()) {
			if ($tournament->paid_flag) {
				$leaderboard = $leaderboard_model->getLeaderBoardRank($tournament, 50, true);
			} else {
				$leaderboard = $leaderboard_model->getLeaderBoardRank($tournament, 50);
			}

			if (!is_null($ticket)) {
				$leaderboard_rank = $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);
			}
		}

		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		$prize_pool   		= $tournament_model->calculateTournamentPrizePool($tournament->id);
		$place_list   		= $tournament_model->calculateTournamentPlacesPaid($tournament, count($player_list), $prize_pool);

		$jackpot_link	= null;
		$parent_link	= null;
		if (!empty($tournament->jackpot_flag)) {
			$tournament_type 	= $is_racing_tournament ? 'racing' : 'sports';
			$jackpot_link 		= 'tournament/'.$tournament_type.'/component/jackpotmap/' . $tournament->id;
			$parent_link 		= 'tournament/details/'. $tournament->parent_tournament_id;
		}

		$unregister_allowed = false;
		$register_allowed 	= false;

		if (!$user->guest) {
			$unregister 		= $tournament_model->unregisterAllowed($tournament->id, $user->id);
			if (!is_null($unregister)) {
				$unregister_allowed = (floor($unregister->time) > 0 && $unregister->bet == 0 && !is_null($ticket));
			}
			$register_allowed 	= (is_null($ticket) && strtotime($tournament->end_date) > time() && empty($tournament->cancelled_flag));
		}
		/**
		 * Code for Comment box
		 */

		$tournament_comment_model 	=& $this->getModel('TournamentComment', 'TournamentModel');
		$tournament_comment_list	= $tournament_comment_model->getTournamentCommentListByTournamentId($tournament->id);

		$display_sledge_box 	= false;
		$allow_sledge_comment	= true;
		/**
		 * The Sledge box will appear
		 * Max 48 hrs after the tournament ends
		 */
		if ((time() - strtotime($tournament->end_date))/(60*60) <= 48) {
			$display_sledge_box = true;
			/**
			 * If the user is Normal user,
			 * he needs to be resigstered to that tournament
			 * To post a comment
			 */
			if (is_null($ticket) && ($user->usertype == "Registered" || $user->guest)) {
				$allow_sledge_comment = false;
			}
		}

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->setLayout('details');

		$view->assignRef('tournament', $tournament);
		$view->assignRef('private_tournament', $private_tournament);
		$view->assignRef('private_tournament_prize_format', $private_tournament_prize_format);
		$view->assignRef('tournament_owner', $tournament_owner);

		$view->assignRef('ticket', $ticket);
		$view->assignRef('user', $user);

		$view->assign('place_list', $place_list);
		$view->assign('prize_pool', $prize_pool);

		$view->assign('unregister_allowed', $unregister_allowed);
		$view->assign('register_allowed', $register_allowed);
		$view->assign('next_match', $next_match);

		$view->assignRef('player_list', $player_list);
		$view->assignRef('leaderboard', $leaderboard);

		$view->assignRef('leaderboard_rank', $leaderboard_rank);
		$view->assign('jackpot_link', $jackpot_link);
		$view->assign('parent_link', $parent_link);
		$view->assign('private_tournament_link', $private_tournament_link);
		$view->assign('is_racing_tournament', $is_racing_tournament);

		$view->assign('tournament_completed', $tournament_completed);
		$view->assign('tournament_comment_list', $tournament_comment_list);
		$view->assign('display_sledge_box', $display_sledge_box);
		$view->assign('allow_sledge_comment', $allow_sledge_comment);

		$view->assign('private_tournament_url', $private_tournament_url);
		$view->assign('shorten_url', $shorten_url);
		$view->display();
	}
	/**
	 * Display Tournament Password form
	 */
	public function confirmPassword()
	{
		$tournament_id = JRequest::getVar('id', null);
		$view =& $this->getView('Tournament', 'html', 'TournamentView');

		$view->setLayout('tournamentpassword');
		$view->assign('tournament_id', $tournament_id);

		$view->display();
	}

	/**
	 * Display Private tournament Email frined form
	 */
	public function emailFriend()
	{
		$tournament_id 				= JRequest::getVar('id', null);
		$private_tournament_model 	=& $this->getModel('TournamentPrivate', 'TournamentModel');
		$tournament_model 			=& $this->getModel('Tournament', 'TournamentModel');

		$user =& JFactory::getUser();
		$previous_tournament = $private_tournament_model->getTournamentPrivateByUserID($user->id, $tournament_id);

		/**
		 * Setting Replacement worlds for Promo email
		 */
		$sport_model 		=& $this->getModel('TournamentSport', 'TournamentModel');
		$is_racing	 		= $sport_model->isRacingByTournamentId($tournament_id);
		$private_tournament = $private_tournament_model->getTournamentPrivateByTournamentID($tournament_id);

		if ($is_racing) {
			$racing_model =& $this->getModel('TournamentRacing', 'TournamentModel');
			$tournament = $racing_model->getTournamentRacingByTournamentID($tournament_id);
			$email_category = 'Racing  – ' . ucfirst($tournament->sport_name) . ' – '. $tournament->meeting_name;
		} else {
			$tournament_sport_event__model 	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
			$tournament = $tournament_sport_event__model->getTournamentSportsByTournamentID($tournament_id);
			$email_category = $tournament->sport_name . ' – ' . $tournament->competition_name . ' – '. $tournament->event_name;
		}

		$replacement_list = array(
			'name'						=> $tournament->name,
			'category'					=> $email_category,
			'private_identifier'		=> $private_tournament->display_identifier,
			'user_id'					=> $user->id
		);
		$replacement_list['promo_email_content'] = '';
		$replacement_list['password_protected']	= $private_tournament->password ? "<br/>You'll need to provide the following password before you can register: {$private_tournament->password}<br/>" : '';

		$email_body = nl2br($this->_getPrivatePromotionalEmailText($replacement_list));

		$view =& $this->getView('Tournament', 'html', 'TournamentView');

		$view->setLayout('emailfriend');
		$view->assign('tournament_id', $tournament_id);
		$view->assign('previous_tournament', $previous_tournament);
		$view->assign('private_tournament_promo_email_text', $email_body);

		$view->display();
	}
	/**
	 * Send out the Private tournament promo email
	 */
	public function sendPrivateTournamentPromoEmail()
	{
		$tournament_id 				= JRequest::getVar('tournament_id', null);
		$previous_tournament 		= JRequest::getVar('previous_tournament', null);
		$tournament_private_emails 	= JRequest::getVar('tournament_private_emails', null);
		$tournament_email_content	= JRequest::getVar('tournament_email_content', null);

		$user =& JFactory::getUser();

		if (!empty($tournament_id) && $user->id > 0) {
			$private_tournament_model 	=& $this->getModel('TournamentPrivate', 'TournamentModel');
			$sport_model  				=& $this->getModel('TournamentSport', 'TournamentModel');

			$is_racing = $sport_model->isRacingByTournamentId($tournament_id);
			$private_tournament = $private_tournament_model->getTournamentPrivateByTournamentID($tournament_id);

			if ($is_racing) {
				$racing_model 	=& $this->getModel('TournamentRacing', 'TournamentModel');
				$tournament 	= $racing_model->getTournamentRacingByTournamentID($tournament_id);
				$email_category = 'Racing  – ' . ucfirst($tournament->sport_name) . ' – '. $tournament->meeting_name;
			}
			else {
				$tournament_sport_event__model 	=& $this->getModel('TournamentSportEvent', 'TournamentModel');
				$tournament 					= $tournament_sport_event__model->getTournamentSportsByTournamentID($tournament_id);
				$email_category 				= $tournament->sport_name . ' – ' . $tournament->competition_name . ' – '. $tournament->event_name;
			}

			$this->setRedirect('/private/'.$private_tournament->display_identifier);

			$tournament_private_emails = str_replace('Enter email addresses', '', $tournament_private_emails);
			/**
			 * Get the email addresses from the textarea
			 */
			if (!empty($tournament_private_emails)) {
				$tournament_private_emails = str_replace(',', '', $tournament_private_emails);
				$tournament_private_emails = str_replace(';', '', $tournament_private_emails);

				$tournament_private_emails = explode("\r\n", trim($tournament_private_emails));
				$error	= false;
				for ($i = 0; $i < sizeof($tournament_private_emails); $i++) {
					if (! JMailHelper::isEmailAddress($tournament_private_emails[$i]) )
					{
						JError::raiseWarning(0, JText::_('One of the email address is invalid') );
						return;
					}
				}
			}
			/**
			 * Get Already registered users for this tournament
			 */
			$previous_entrant_list	 = array();
			$already_registered_list = array();
			$already_registered_list = $private_tournament_model->getFriendsEmailForPrivateTournamentByTournamentID($tournament_id);
			/**
			 * Add tournament owner to already register list
			 */
			if (!in_array($user->email, $already_registered_list)) {
				array_push($already_registered_list, $user->email);
			}

			if ($previous_tournament > 0) {
				/**
				 * Get proevious entrant list
				 */
				$previous_entrant_list 	= $private_tournament_model->getFriendsEmailForPrivateTournamentByTournamentID($previous_tournament);
			}
			/**
			 * Merge together previous users with the new email addresses and exclude them from the invited users
			 */
			if (!empty($tournament_private_emails)) {
				$previous_entrant_list = array_merge($previous_entrant_list, $tournament_private_emails);
			}

			$emails = array_diff($previous_entrant_list, $already_registered_list);

			/**
			 * Sending emails
			 */
			if (!empty($emails)) {
				$replacement_list = array(
					'name'						=> $tournament->name,
					'category'					=> $email_category,
					'private_identifier'		=> $private_tournament->display_identifier,
					'user_id'					=> $user->id
				);

				$replacement_list['promo_email_content'] = $tournament_email_content ? $tournament_email_content : '';
				$replacement_list['password_protected']	= $private_tournament->password ? "<br/>You'll need to provide the following password before you can register: {$private_tournament->password} <br />" : '';

				$this->_sendPromoEmail($emails, $replacement_list);
			}
		}
	}

	private function _sendPromoEmail($emails, $replacement_list = null)
	{
		global $mainframe;

		$user =& JFactory::getUser();
		$subject = JText::_("You're invited to join my Private TopBetta Tournament");

		$mailer = new UserMAIL();

		$email_params	= array(
			'subject'	=> $subject,
			'mailfrom'	=> $user->email,
			'fromname'	=> $user->name,
			'ishtml'	=> true
		);

		foreach ($emails as $email) {
			$email_params['mailto']	= $email;
			$result = $mailer->sendUserEmail('privateTournamentInvitationEmail', $email_params, $replacement_list);
		}

		if ($result == 1) {
			$this->setMessage(JText::_('Success! Invitation email sent to all your friends.'));
		}
		return;
	}
	/**
	 * Match Tournament Password
	 */
	public function matchPassword()
	{
		$id 		= JRequest::getVar('id', null);
		$password 	= trim(JRequest::getVar('given_password'));

		$private_tournament_model 	=& $this->getModel('TournamentPrivate', 'TournamentModel');
		$private_tournament 		= $private_tournament_model->getTournamentPrivateByTournamentID($id);

		if ($private_tournament->password == $password) {
			$sport_model =& $this->getModel('TournamentSport', 'TournamentModel');
			$is_racing_tournament = $sport_model->isRacingByTournamentId($id);

			$tournament_type = $is_racing_tournament ? 'racing' : 'sports';
			$url = $register_link = '/tournament/'. $tournament_type .'/confirmticket/'. $id;

			echo $url;
		}
	}

	private function _generatePrivateTournamentCode($private_tournament_model) {
		$code 		= '';
		$code_pool	= '2346789bcdfghjkmnpqrtvwxyz';
		$pool_len	= strlen($code_pool);

		$i = 0;
		while ($i < 6) {
			$code .= substr($code_pool, mt_rand(0, $pool_len-1), 1);
			$i++;
		}

		if ($private_tournament_model->getTournamentPrivateByIdentifier($code)) {
			$code = $this->_generatePrivateTournamentCode($private_tournament_model);
		}

		return $code;
	}
	/**
	 * Shorten Url
	 */
	public function shortenUrl($url, $format = "xml")
	{

		$bitly_username	= "topbetta";
		$bitly_password = "t0pb3tt@";
		$bitly_api_key	= "R_507d4969ee65d90b7f6058fbe1609444";
		$version		= "2.0.1";

		$bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$bitly_username.'&apiKey='.$bitly_api_key.'&format='.$format;

		//get the url
		//could also use cURL here
		$response = file_get_contents($bitly);

		//parse depending on desired format
		if (strtolower($format) == 'json') {
			$json = @json_decode($response, true);
			return $json['results'][$url]['shortUrl'];
		} else { //xml
			$xml = simplexml_load_string($response);
			return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
		}
	}
	/**
	 * Get Private Tournament Promo Email text
	 */
	private function _getPrivatePromotionalEmailText($replacement_list)
	{
		$config 	=& JComponentHelper::getParams( 'com_topbetta_user' );
		$email_body = $config->get('privateTournamentInvitationEmailText');

		foreach ($replacement_list as $needle => $replacement) {
			$email_body = str_replace("[$needle]", $replacement, $email_body);
		}

		return $email_body;
	}

	/**
	 * Display the ticket confirmation page
	 *
	 * @return void
	 */
	public function confirmTicket()
	{
		$this->validateTicket(false);
	}

	/**
	 * Save a ticket
	 *
	 * @return void
	 */
	public function saveTicket()
	{
		$this->validateTicket(true);
	}

	/**
	 *
	 */

	public function ticketError($error, $redirect = false, $tournament = null)
	{
		if ($redirect) {
			$url = '/tournament/sports';
			/**
			 * check the url to direct to sport or racing
			 */
			if (!is_null($tournament)) {
				$url = '/tournament/details/' . $tournament->id;
			}
			$this->setRedirect($url, $error, 'error');
		} else {
			JRequest::setVar('task', 'ticketerror');

			$view =& $this->getView('Tournament', 'html', 'TournamentView');

			$view->assign('error', $error);
			$view->assignRef('tournament', $tournament);
			$view->display();
		}
	}
	/**
	 * Display the ticket confirmation box
	 *
	 * @param object $tournament
	 * @return void
	 */
	protected function displayTicket($tournament)
	{
		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		$tournament->entrants = $ticket_model->countTournamentEntrants($tournament->id);

		$tournament_model =& $this->getModel('Tournament', 'TournamentModel');

		$tournament->prize_pool = $tournament_model->calculateTournamentPrizePool($tournament->id);
		$tournament->places_paid = $tournament_model->calculateTournamentPlacesPaid($tournament, $tournament->entrants, $tournament->prize_pool);

		$view =& $this->getView('Tournament', 'html', 'TournamentView');
		$view->assignRef('tournament', $tournament);

		$view->display();
	}
	/**
	 * Validate ticket
	 */
	protected function validateTicket($save = false)
	{
		$id				= JRequest::getVar('id', null);
		$sport_model 	=& $this->getModel('TournamentSport', 'TournamentModel');
		$isRacing		= $sport_model->isRacingByTournamentId($id);

		if ($isRacing > 0) {
			$tournament_model =& $this->getModel('TournamentRacing', 'TournamentModel');
			$tournament = $tournament_model->getTournamentRacingByTournamentID($id);
		} else {
			$tournament_model =& $this->getModel('TournamentSportEvent', 'TournamentModel');
			$tournament = $tournament_model->getTournamentSportsByTournamentID($id);

			if (strtotime($tournament->betting_closed_date) < time()) {
				return $this->ticketError(JText::_('Betting has already closed'), $save, $tournament);
			}
		}
		
		if (strtotime($tournament->end_date) < time()) {
			return $this->ticketError(JText::_('Tournament has already finished'), $save, $tournament);
		}
		if ($tournament->cancelled_flag) {
			return $this->ticketError(JText::_('Tournament has cancelled'), $save, $tournament);
		}

		$user =& JFactory::getUser();

		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');
		$ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id);

		if (!is_null($ticket)) {
			return $this->ticketError(JText::_('You already have a ticket for this tournament'), $save, $tournament);
		}

		$tournament->isRacing = $isRacing;

		if (!empty($tournament->entry_fee) && !empty($tournament->buy_in)) {
			
			//check the user status
			$tb_status_model 			=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
				
			if(!$tb_status_model->isTopbettaUser($user->id) ){
				return $this->ticketError(JText::_('You have a basic account. Please upgrade it to enter a paid tournament.<br><a href="/user/upgrade" >Upgrade my account</a>'), $save, $tournament);
				$this->setRedirect('/user/upgrade', JText::_('You have a basic account. Please upgrade it to enter a paid tournament'), 'error');
			}	
			
			if (empty($user->tournament_dollars) || empty($user->tournament_dollars)) {
				return $this->ticketError(JText::_('You are not allowed to enter tournaments'), $save, $tournament);
			}

			$tournament_dollars = $user->tournament_dollars->getTotal();
			$account_balance    = $user->account_balance->getTotal();

			$value = $tournament->entry_fee + $tournament->buy_in;
			if ($value > ($tournament_dollars + $account_balance)) {
				return $this->ticketError(JText::_('Insufficient funds to purchase the ticket'), $save, $tournament);
			}
			
			//check the account balance spent with bet limit
			$account_balance_spent =  $tournament->entry_fee + $tournament->buy_in - $tournament_dollars;
			if ($account_balance_spent > 0 && !$this->_checkBetLimit($account_balance_spent)) {
				return $this->ticketError(JText::_('Exceed your bet limit'), $save, $tournament);
			}
		}

		if ($save) {
			$this->storeTicket($tournament, $user);
		} else {
			$this->displayTicket($tournament);
		}
	}

	/**
	 * Store a ticket purchase in the database
	 *
	 * @param object $tournament
	 * @param object $user
	 * @return void
	 */
	protected function storeTicket($tournament, $user)
	{
		$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');

		$buy_in_id      = $user->tournament_dollars->decrement($tournament->buy_in, 'buyin');
		$entry_fee_id   = $user->tournament_dollars->decrement($tournament->entry_fee, 'entry');

		$ticket = array(
				'tournament_id'             => $tournament->id,
				'user_id'                   => $user->id,
				'entry_fee_transaction_id'  => $entry_fee_id,
				'buy_in_transaction_id'     => $buy_in_id,
				'refunded_flag'             => 0,
				'resulted_flag'             => 0
		);

		$ticket_id = $ticket_model->store($ticket);
		$betting_closed_date = ($tournament->betting_closed_date ? $tournament->betting_closed_date : $tournament->end_date);
		if ($this->confirmAcceptance( $ticket_id, $user->id, 'tournamentticket', strtotime($betting_closed_date) )) {
			$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');
			$leaderboard = array(
						'user_id'       => $user->id,
						'tournament_id' => $tournament->id,
						'currency'      => $tournament->start_currency
			);

			$leaderboard_model->store($leaderboard);

			$sport_model =& $this->getModel('TournamentSport', 'TournamentModel');
			$is_racing_tournament = $sport_model->isRacingByTournamentId($tournament->id);
			$tournament_type = $is_racing_tournament ? 'racing' : 'sports';

			$url = '/tournament/'.$tournament_type.'/game/' . $tournament->id;
			$message = JText::_('Ticket purchase confirmed');
			$type = 'message';
			
			//Check wether FB user or not
			$user_model		=& $this->getModel('TopbettaUser','TopbettaUserModel');
			
	        if($user->entriesToFbWall == 1 && $user_model->isFacebookUser($user->id)){
				
				require_once JPATH_ROOT.DS.'components'.DS.'com_jfbconnect'.DS.'libraries'.DS.'facebook.php';
				$jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
				
				$post['caption'] = 'I am a TopBetta!!!';
				$post['message'] = 'I have just entered the '.$tournament->name.' tournament. Come and challenge me to see who is the TopBetta!';
				$post['link'] = 'https://www.topbetta.com'.$url;
				$post['picture'] = 'https://www.topbetta.com/templates/topbetta/images/topbetta-logo.jpg';
                 
				//if ($jfbcLibrary->getUserId()){ // Check if there is a Facebook user logged in
                    
					if($jfbcLibrary->setFacebookMessageWall($post,$user->id))
						//$jfbcLibrary->setFacebookMessage($post);
						$message .= JText::_(' and posted on your Facebook wall.');
					else $message .= JText::_(' and failed to post on your Facebook wall.');
					
				//}
			}
			
		} else {
			$ticket = $ticket_model->getTournamentTicket($ticket_id);
			$refund_id = $user->tournament_dollars->increment($tournament->buy_in + $tournament->entry_fee, 'refund');

			$ticket->refunded_flag = 1;
			$ticket->result_transaction_id = $refund_id;

			$ticket_model->store((array)$ticket);

			$url = '/tournament/details/' . $tournament->id;
			$message = JText::_('Ticket could not be purchased');
			$type = 'error';
		}
		$this->setRedirect($url, $message, $type);
	}

	/**
	 * Call the processing server for acceptance if required
	 *
	 * @param integer $transaction_id
	 * @param integer $user_id
	 * @param string  $type
	 * @param integer $deadline
	 * @return boolean
	 */
	protected function confirmAcceptance($transaction_id, $user_id, $type, $deadline)
	{
		$config =& JFactory::getConfig();
		$enabled = $config->getValue('config.remote_processing');

		if ($enabled) {
			$processing = array(
				'method'          	=> 'confirm_acceptance',
				'transaction_id'  	=> $transaction_id,
				'user_id'         	=> $user_id,
				'type'            	=> $type,
				'initiated_date'  	=> time(),
				'deadline_date'   	=> $deadline
			);

			$response = TournamentHelper::callProcessingServer($processing);
			return (!empty($response) && $response->status == 'accepted');
		}

		return true;
	}
	/**
	 * Display the jackpot map
	 *
	 * @return void
	 */
	public function jackpotMap() {
		$id = JRequest::getVar('id', null);

		$sport_model			=& $this->getModel('TournamentSport', 'TournamentModel');
		$is_racing_tournament 	= $sport_model->isRacingByTournamentId($id);

		if ($is_racing_tournament){
			$racing_model =& $this->getModel('TournamentRacing', 'TournamentModel');
		}

		$tournament_model 	=& $this->getModel('Tournament', 'TournamentModel');
		$ticket_model 		=& $this->getModel('TournamentTicket', 'TournamentModel');

		$map = array();
		while (!empty($id) && $id != -1) {
			if ($is_racing_tournament) {
				$current = $racing_model->getTournamentRacingByTournamentID($id);
			} else {
				$current = $tournament_model->getTournament($id);
			}

			if (!is_null($id)) {
				$current->entrants    = $ticket_model->countTournamentEntrants($id);
				$current->prize_pool  = $tournament_model->calculateTournamentPrizePool($id);

				$map[] = $current;
				$id = $current->parent_tournament_id;
			}
		}

		$view =& $this->getView('Tournament', 'html', 'TournamentView');

		$view->assignRef('jackpot_map', $map);

		$view->display();
	}
	/**
	 * Set user redirect
	 */
	public function setUserRedirect()
	{
		$url	= JRequest::getVar('url', 'index.php');
		$txt	= JRequest::getVar('text', null);
		$type	= JRequest::getVar('type', 'error');
		$this->setRedirect($url, JText::_($txt), $type);
	}

	/**
	 * Unregister from a tournament and refund the buy-in if there was one.
	 *
	 * @return void
	 */
	public function unregister()
	{
		$id = JRequest::getVar('id', null);
		if (is_null($id)) {
			$message  = JText::_('No tournament specified');
			$type     = 'error';
		} else {
			$tournament_model =& $this->getModel('Tournament', 'TournamentModel');

			if ($tournament = $tournament_model->getTournament($id)) {

				$user =& JFactory::getUser();

				$unregister = $tournament_model->unregisterAllowed($tournament->id, $user->id);
				$unregister_allowed = (floor($unregister->time) > 0 && $unregister->bet == 0);

				if ($unregister_allowed) {
					if (strtotime($tournament->start_date) > time()) {
						$ticket_model =& $this->getModel('TournamentTicket', 'TournamentModel');

						if ($ticket = $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament->id)) {
							if ($ticket_model->refundTicket($ticket->id, true)) {
								$leaderboard_model =& $this->getModel('TournamentLeaderboard', 'TournamentModel');
								$leaderboard_model->deleteByUserAndTournamentID($user->id, $tournament->id);

								$message  = JText::_('Ticket has been refunded');
								$type     = 'message';
							} else {
								$message  = JText::_('Ticket could not be refunded');
								$type     = 'error';
							}
						} else {
							$message  = JText::_('Ticket not found');
							$type     = 'error';
						}
					} else {
						$message  = JText::_('Can\'t refund a ticket for a tournament which has commenced');
						$type     = 'error';
					}
				} else {
					$message  = JText::_('You\'ve already bet on this tournament');
					$type     = 'error';
				}
			} else {
				$message  = JText::_('Tournament not found');
				$type     = 'error';
			}
		}

		$this->setRedirect('index.php', $message, $type);
	}
	/**
	 * Saving Comment
	 */
	public function saveComment()
	{
		$comment 			= strip_tags(JRequest::getVar('tournament_sledge', '')); //cleaning up the html
		$tournament_id		= JRequest::getVar('tournament_id', null);
		$display_identifier	= JRequest::getVar('display_identifier', null);
		$ticket_model 		=& $this->getModel('TournamentTicket', 'TournamentModel');

		if (!empty($display_identifier)) {
			$redirect_url = JURI::base()."private/".$display_identifier;
		} else {
			$redirect_url = JURI::base() . "tournament/details/" . $tournament_id;
		}

		$this->setRedirect($redirect_url);

		$user =& JFactory::getUser();
		if ($user->guest) {
			JError::raiseWarning(0, JText::_('Please login to post a comment'));
			return false;
		}
		/**
		 * If the user is Normal user,
		 * he needs to be resigstered to the tournament
		 * To post a comment
		 */
		$ticket	= $ticket_model->getTournamentTicketByUserAndTournamentID($user->id, $tournament_id);

		/**
		 * If the user is Normal user,
		 * he needs to be resigstered to that tournament
		 * To post a comment
		 */
		if (is_null($ticket) && ($user->usertype == "Registered" || $user->guest)) {
			JError::raiseWarning(0, JText::_('You need to be in the tournament to post a comment'));
			return false;
		}
		if (strlen($comment) > 400) {
			JError::raiseWarning(0, JText::_('You are allowed maximum 400 characters per post'));
			return false;
		}
		if(empty($comment)){
			JError::raiseWarning(0, JText::_("Comment can't be Empty"));
			return false;
		}
		/**
		 * Replace bad words with asterisks
		 */
		$config 		=& JComponentHelper::getParams( 'com_topbetta_user' );
		$blacklist 		= $config->get('blacklistWords');
		$blacklist		= explode("\n", $blacklist); //Array of blacklisted words
		foreach ($blacklist as $key => $badword){
			/**
			 * Replacing all characters of the bad word with *
			 */
			$comment = str_replace($badword, str_repeat('*', strlen($badword)), $comment);
		}

		$tournament_comment_model 	=& $this->getModel('TournamentComment', 'TournamentModel');

		$params	= array(
			"tournament_id" => $tournament_id,
			"user_id" => $user->id,
			"comment" => $comment
		);

		$result	= $tournament_comment_model->store($params);

		$this->setMessage(JText::_("Comment Posted!"));
		return true;
	}

}
