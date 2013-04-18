<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

/**
 * Betting controller
 */
class BettingController extends JController {

	/**
	 * Constructor which will check authentication on required tasks
	 */
	public function __construct($implement_task = true)
	{
		if ($implement_task) {
			$authenticate = array(
				'bettinghistory'
			);
			
			$user =& JFactory::getUser();
			$task = JRequest::getVar('task', 'display');
		}
		
		parent::__construct();
		
		if ($implement_task && $user->guest && in_array($task, $authenticate)) {
      		$msg = JText::_("You need to login to access this part of the site.");
			$this->setRedirect('/user/register', $msg, 'error');
			$this->redirect();
		}
	}
	/**
	 * Display today's racing list
	 *
	 * @return void
	 */
	public function display()
	{
		JRequest::setVar('task', 'list');
		
		$meeting_model		=& $this->getModel('Meeting', 'TournamentModel');
		$race_model			=& $this->getModel('Race', 'TournamentModel');
		$race_status_model	=& $this->getModel('EventStatus', 'TournamentModel');
		$result_model		=& $this->getModel('SelectionResult', 'TournamentModel');
		$runner_model		=& $this->getModel('Runner', 'TournamentModel');
		
		$meeting_list	= $meeting_model->getTodayActiveMeetingList();
		
		foreach ($meeting_list as $meeting) {
			$meeting->race_list = $race_model->getRaceListByMeetingID($meeting->id);
		}
		
		$view =& $this->getView('Betting', 'html', 'BettingView');
		$view->assign('meeting_list', $meeting_list);
		$view->assign('open_id', JRequest::getVar('open_id', 0));

		$view->setModel($result_model);
		$view->setModel($runner_model);
		$view->setModel($race_status_model);
		
		$view->display();
	}
	
	public function listrunner()
	{
		$this->meeting();
	}
	
	/**
	 * Display meeting (game play) page
	 *
	 * @return void
	 */
	public function meeting($view = null)
	{
		$session =& JFactory::getSession();
		
		foreach (array('error', 'message') as $msg_type) {
			$sess_msg		= 'sess_' . $msg_type;
			$betting_error	= stripslashes($session->get($sess_msg, null, 'betting'));
			if ($betting_error) {
				JFactory::getApplication()->enqueueMessage(JText::_($betting_error), $msg_type );
			}
			$session->clear($sess_msg, 'betting');
		}
		
		$meeting_id = JRequest::getVar('meeting_id', null);
		if (is_null($meeting_id)) {
			$this->setRedirect('/', JText::_('No meeting selected'), 'error');
			return;
		}
		
		$meeting_model	=& $this->getModel('Meeting', 'TournamentModel');
		$meeting		= $meeting_model->getMeeting($meeting_id);
		
		if (is_null($meeting)) {
			$this->setRedirect('index.php', JText::_('Meeting not found'), 'error');
			return;
		}
		
		$competition_model	=& $this->getModel('TournamentCompetition', 'TournamentModel');
		$competition		= $competition_model->getTournamentCompetition($meeting->tournament_competition_id);
		
		$meeting->type		= $competition->name;
		
		$race_model	=& $this->getModel('Race', 'TournamentModel');
		
		$number = JRequest::getVar('number', $race_model->getNextRaceNumberByMeetingID($meeting->id));
		if(is_null($number)) {
			$number = $race_model->getLastRaceNumberByMeetingID($meeting->id);
		}
		
		$race = $race_model->getRaceByMeetingIDAndNumber($meeting->id, $number);
		
		if(is_null($race)) {
			$this->setRedirect('/', JText::_('No race data available'), 'error');
			return;
		}
		
		$status_model	=& $this->getModel('EventStatus', 'TournamentModel');
		$status			= $status_model->getEventStatus($race->event_status_id);

		$race->status = $status->name;
		
		$race_time_list = $race_model->getRaceTimesByMeetingID($meeting->id);

		$runner_model	=& $this->getModel('Runner', 'TournamentModel');
		$runner_list	= $runner_model->getRunnerListByRaceID($race->id);
		
		$runner_list_by_id		= array();
		$runner_list_by_number	= array();
		$runner_ident_list		= array();
		foreach($runner_list as $runner) {

			$runner_list_by_id[$runner->id]			= $runner;
			$runner_list_by_number[$runner->number]	= $runner;
			$runner_ident_list[]					= $runner->ident;

			$bet_product_id 						= $runner->bet_product_id;
		}
		
		$rating_list	= array();
		$rating_list 	= $runner_model->getRunnerRatings($runner_ident_list);
		
		$runner_count 	= 0;
		
		foreach ($runner_list as $runner) {
			$runner->rating	= isset($rating_list[$runner->ident]) ? $rating_list[$runner->ident]->rating : 0;
			
			if ($runner->status=='Not Scratched') {
				$runner_count++;
			}
		}
		
		$bet_type_model =& $this->getModel('BetType', 'BettingModel');
		$bet_type_list 	= $bet_type_model->getBetTypesByStatus(1, 'racing'); // default is enabled

		if ($runner_count <= 4) {
			$bet_type_list = array();
			$bet_type_list[] = $bet_type_model->getBetTypeByName('win');
			
			switch ($runner_count) {
				case 4:
					$bet_type_list[] = $bet_type_model->getBetTypeByName('firstfour');
				case 3:
					$bet_type_list[] = $bet_type_model->getBetTypeByName('quinella');
					$bet_type_list[] = $bet_type_model->getBetTypeByName('exacta');
					$bet_type_list[] = $bet_type_model->getBetTypeByName('trifecta');
					break;
			}
		}
		
		$user =& JFactory::getUser();
		
		$wagering_bet_list	= array();
		$bet_list			= array();
		
		$result_model	=& $this->getModel('SelectionResult', 'TournamentModel');
		$result_list	= $result_model->getSelectionResultListByEventID($race->id);


		if (is_null($view)) {
			$view	=& $this->getView('Betting', 'html', 'BettingView');
			$layout	= JRequest::getVar('layout', 'meeting');
			$view->setLayout($layout);
		}

		$view->assignRef('bet_type_list', $bet_type_list);
		$view->assignRef('competition', $competition);
		$view->assignRef('meeting', $meeting);
		$view->assignRef('race', $race);
		
		$view->assignRef('result_list', $result_list);
		
		$view->assignRef('race_time_list', $race_time_list);
		
		$view->assignRef('runner_list', $runner_list);
		$view->assignRef('runner_list_by_id', $runner_list_by_id);
		$view->assignRef('runner_list_by_number', $runner_list_by_number);
		
		//for user status
		$tb_status_model 			=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
			if($tb_status_model->isTopbettaUser(JFactory::getUser()->id)) 
			{
				$view->assign('tb_user', true);
			}
			else
			{
				$view->assign('tb_user', false);
			}

		$this->_setBetListView($view, $user, $race);
		
		$view->display();
	}
	
	protected function _setBetListView($view, $user, $race)
	{
		$wagering_bet_list	= array();
		$bet_list			= array();
		
		if (!$user->guest) {
			$bet_model		=& $this->getModel('Bet', 'BettingModel');
			$bet_type_model	=& $this->getModel('BetType', 'BettingModel');
			$betting_list	= $bet_model->getBetListByUserIDAndEventID($user->id, $race->id);
			
			$bet_selection_model =& $this->getModel('BetSelection', 'BettingModel');
			
			foreach ($betting_list as $bet) {
				$bet_type		= $bet_type_model->getBetType($bet->bet_type_id);
				$selection_list	= $bet_selection_model->getBetSelectionListByBetID($bet->id);
				
				$boxed_flag			= $bet_model->isBoxedBet($bet->id);
				$is_exotic_bet_type	= $this->_isExoticBetType($bet_type->name);
				
				if ($is_exotic_bet_type) {
					$wagering_bet	= WageringBet::newBet($bet_type->name, $bet->bet_amount, $boxed_flag, $bet->flexi_flag);
				} else {
					$wagering_bet	= WageringBet::newBet($bet_type->name, $bet->bet_amount);
				}
				
				foreach ($selection_list as $selection) {
					$position = null;
					if ($is_exotic_bet_type) {
						$position = ($boxed_flag ? null : $selection->position);
					}
					$wagering_bet->addSelection($selection->number, $position);
				}
				
				$wagering_bet_list[$bet->id]	= $wagering_bet;
				$bet_list[$bet->id]				= $bet;
			}
		}
		
		$view->assignRef('bet_list', $bet_list);
		$view->assignRef('wagering_bet_list', $wagering_bet_list);
	}
	
	/**
	 * Ajax call to validate bets
	 *
	 * @return void
	 */
	public function ajaxValidateBet()
	{
		$ajax_callback = new stdClass();
		
		$validation = $this->_validateBet();
		$ajax_callback->error		= $validation->error;
		$ajax_callback->relogin	= $validation->relogin; 
		if (!$validation->error) {
			$ajax_callback->bet_type_name	= $validation->data['bet_type']->name;
			$ajax_callback->data			= serialize($validation->data);
		}
		print json_encode($ajax_callback);
		return;
	}
	
	/**
	 * Validate bet selection
	 *
	 * @return void
	 */
	private function _validateBet($free_bet_amount='off')
	{
		$validation = new stdClass();
		$validation->relogin	= false;
		$validation->error		= false;
		$validation->data		= array();
		
		$user =& JFactory::getUser();
		
		if ($user->guest) {
			$validation->relogin	= true;
			$validation->error		= JText::_('Session expired. Please re-login');
			return $validation;
		}
		else 
		{
			//check the user status
			$tb_status_model 			=& $this->getModel('TopbettaUser', 'TopbettaUserModel');
				
			if(!$tb_status_model->isTopbettaUser($user->id) ){
				$validation->error		= JText::_('You have a basic account. Please upgrade it to place a bet. <br><a href="/user/upgrade" >Upgrade my account</a>' );
				$this->setRedirect('/user/upgrade', JText::_('You have a basic account. Please upgrade it to place a bet'), 'error');
				exit;
			}		
		}
		
		$id = JRequest::getVar('id', null);
		if (is_null($id)) {
			$validation->error = JText::_('No meeting specified');
			return $validation;
		}
		
		$meeting_model =& $this->getModel('Meeting', 'TournamentModel');
		$meeting = $meeting_model->getMeeting($id);
		
		if (is_null($meeting)) {
			$validation->error = JText::_('Meeting not found');
			return $validation;
		}
		
		$race_id = JRequest::getVar('race_id', null);
		if (is_null($race_id)) {
			$validation->error = JText::_('No race specified');
			return $validation;
		}
		
		$race_model =& $this->getModel('Race', 'TournamentModel');
		$race = $race_model->getRace($race_id);
		if (is_null($race)) {
			$validation->error = JText::_('Race was not found');
			return $validation;
		}
		
		$race_status_model	=& $this->getModel('EventStatus', 'TournamentModel');
		$selling_status		=$race_status_model->getEventStatusByKeyword('selling');
		if ($race->event_status_id != $selling_status->id) {
			$validation->error = JText::_('Betting was closed');
			return $validation;
		}
		
		$bet_model	=& $this->getModel('Bet', 'BettingModel');
		if (time() - $bet_model->getLastBetTimeStampByUserID($user->id) < 2) {
			$validation->error = JText::_('Please wait a second to make another bet');
			return $validation;
		}
		
		$bet_type_id = JRequest::getVar('bet_type_id', null);
		if (is_null($bet_type_id)) {
			$validation->error = JText::_('No bet type selected');
			return $validation;
		}
		
		$bet_type_model =& $this->getModel('BetType', 'BettingModel');
		$bet_type = $bet_type_model->getBetType($bet_type_id, true);

		if (is_null($bet_type)) {
			$validation->error = JText::_('Invalid bet type selected');
			return $validation;
		}
		
		$value = JRequest::getVar('value', null);
		$value = $value * 100;

		$selection_list = JRequest::getVar('selection', array());
		if (empty($selection_list)) {
			$validation->error = JText::_('Invalid bet selections');
			return $validation;
		}
		
		$runner_model =& $this->getModel('Runner', 'TournamentModel');
		$runner_list = $runner_model->getRunnerListByRaceID($race->id);
		
		$runner_list_by_id		= array();
		$runner_list_by_number	= array();
		foreach ($runner_list as $runner) {
			$runner_list_by_id[$runner->id]			= $runner;
			$runner_list_by_number[$runner->number]	= $runner;
		}
		
		foreach ($selection_list as $selections) {
			foreach ($selections as $selection_id) {
				if (!isset($runner_list_by_id[$selection_id])) {
					$validation->error = JText::_('One or more selected runners were not found in this race');
					return $validation;
				}
			}
		}
		
		$boxed_flag = $this->_isBoxedBet($bet_type->name, $selection_list);
		$flexi_flag = $this->_isFlexiBet($bet_type->name, $selection_list);
		
		$is_exotic_bet_type = $this->_isExoticBetType($bet_type->name);
		
		$wagering_bet_list	= array();
		$bet_total			= 0;
		
		$bet_record = (strtolower($bet_type->name) == 'eachway') ? array('win', 'place') : array($bet_type->name);
		foreach($bet_record as $type) {
		
		if ($is_exotic_bet_type) {
			
			$bet = WageringBet::newBet($type, $value, $boxed_flag, $flexi_flag, unserialize($race->external_race_pool_id_list));
	
			foreach ($selection_list as $pos => $selections) {
				
				$position_number = null;
				if (!$boxed_flag) {
					$position_number = BettingHelper::getPositionNumber($pos);
					
					if (is_null($position_number)) {
						$validation->error = JText::_('Invalid position number');
						return $validation;
					}
				}
				
				foreach ($selections as $selection_id) {
					$bet->addSelection($runner_list_by_id[$selection_id]->number, $position_number);
				}
			}
				
			if (!$bet->isValid()) {
				$validation->error = JText::_($bet->getErrorMessage());
				return $validation;
				
			} else {
				$wagering_bet_list[] = $bet;
				$bet_total	+= $bet->getTotalBetAmount();
			}
		} else {
			foreach ($selection_list['first'] as $selection_id) {
				$bet = WageringBet::newBet($type, $value, false, 0, unserialize($race->external_race_pool_id_list));
				$bet->addSelection($runner_list_by_id[$selection_id]->number);
				
				if (!$bet->isValid()) {
					$validation->error = JText::_($bet->getErrorMessage());
					return $validation;
				} else {
					$wagering_bet_list[] = $bet;
					$bet_total	+= $bet->getTotalBetAmount();
				}
			}
		}
		
	}
	

		//get the users TOTAL funds available inc free credit
		$user_account_total = $user->account_balance->getTotal() + $user->tournament_dollars->getTotal();
		//$user_account_total	= ($free_bet_amount == 0) ? $user_account_total - $user->tournament_dollars->getTotal() : $user_account_total;
		
		$validation->data['wagering_bet_list'] = $wagering_bet_list;
		
		//check user account balance
		if ($bet_total > $user_account_total) {
			$validation->error = JText::_('Insufficient funds to bet');
			return $validation;
		}
		
		if (!$this->_checkBetLimit($bet_total)) {
			$validation->error = JText::_('Exceed your bet limit');
			return $validation;
		}
		
		$api = WageringApi::getInstance(WageringApi::API_TOB);
		
		$api_con=$api->checkConnection();
		if(is_null($api_con))
		{
		$validation->error = JText::_('Service Not Available. Please Try Again Shortly');
		return $validation;
		}
		
		$bet_origin			= JRequest::getVar('bet_origin', null);
		
		if ($bet_origin != 'tournament') {
			$bet_origin = 'betting';
		}
		
		$validation->data['flexi_flag']				= (int)$flexi_flag;
		$validation->data['meeting']				= $meeting;
		$validation->data['race']					= $race;
		$validation->data['bet_type']				= $bet_type;
		$validation->data['runner_list_by_id'] 		= $runner_list_by_id;
		$validation->data['runner_list_by_number']	= $runner_list_by_number;
		$validation->data['bet_origin']				= $bet_origin;
		
		return $validation;
	}
	
	private function _isExoticBetType($bet_type_name)
	{
		if (in_array($bet_type_name,WageringBet::$standard_bet_type_list)) {
			return false;
		}
		return true;
	}
	
	private function _isBoxedBet($bet_type_name, $selection_list)
	{
		
		if (!$this->_isExoticBetType($bet_type_name)) {
			return false;
		}
		
		unset($selection_list['first']);
		return count($selection_list) == 0;
	}
	
	private function _isFlexiBet($bet_type_name)
	{
		
		if ($bet_type_name != WageringBet::BET_TYPE_TRIFECTA && $bet_type_name != WageringBet::BET_TYPE_FIRSTFOUR) {
			return false;
		}
		
		return JRequest::getVar('flexi', false);
	}
	
	public function confirmBet()
	{
		$data			= JRequest::getVar('data', null, 'post');
		$bet_type_name	= JRequest::getVar('bet_type_name', null, 'post');
		
		if (empty($data)) {
			$this->setRedirect('/', JText::_('Invalid parameter'), 'error');
			exit;
		}
		
		$this->_loadUnserializeRequiredClasses($bet_type_name);
		
		$data					= unserialize($data);
		$race					= $data['race'];
		$bet_type				= $data['bet_type'];
		$meeting				= $data['meeting'];
		$wagering_bet_list		= $data['wagering_bet_list'];
		$runner_list_by_id		= $data['runner_list_by_id'];
		$runner_list_by_number	= $data['runner_list_by_number'];
		$bet_origin				= $data['bet_origin'];
		
		if (is_null($race) || is_null($bet_type) || empty($wagering_bet_list)) {
			$this->setRedirect('/', JText::_('Invalid parameter'), 'error');
			exit;
		}

		$selection_list = JRequest::getVar('selection', array());
		
		$bet_total = 0;
		foreach ($wagering_bet_list as $bet) {
			$bet_total	+= $bet->getTotalBetAmount();

		}
		
		$runner_model	=& $this->getModel('Runner', 'TournamentModel');
		$runner_list	= $runner_model->getRunnerListByRaceID($race->id);

		$bet_product_model	=& JModel::getInstance('BetProduct', 'BettingModel');
		


		$bet_product = array();
		foreach($runner_list as $runner){
			if($runner->bet_product_id != "")
			{ 
				if($bet_type->id >= 4){ // If exotics, then display SuperTab tote
					$bet_product[$runner->number]['name']['exotics'] = $bet_product_model->getBetProduct(4)->name;
				} else {
					if($bet_type->id == 1)
						$bet_product[$runner->number]['name']['win'] = $bet_product_model->getBetProduct($runner->w_product_id)->name;
					elseif($bet_type->id == 2)
						$bet_product[$runner->number]['name']['place'] = $bet_product_model->getBetProduct($runner->p_product_id)->name;
					elseif($bet_type->id == 3) {
						$bet_product[$runner->number]['name']['win'] = $bet_product_model->getBetProduct($runner->w_product_id)->name;
						$bet_product[$runner->number]['name']['place'] = $bet_product_model->getBetProduct($runner->p_product_id)->name;
					}
					else
						$bet_product[$runner->number]['name']['other'] = $bet_product_model->getBetProduct($runner->bet_product_id)->name;
				}

				$bet_product[$runner->id]['id'] = $runner->bet_product_id; 
			}
		}
		//$bet_product_name = $bet_product_model->getBetProduct($bet_product_id);
		
		//Get torunament balace
		$user = JFactory::getUser();
		$balances = array('account_balance', 'tournament_dollars');
		foreach($balances as $balance) {
			$amount = 0;
			if(!empty($user->$balance)) {
				$user->$balance->setUserId($user->get('id'));
				$amount = $user->$balance->getTotal();
				if(!empty($amount)) {
					$amount = $amount / 100;
				}
			}
			$funds[$balance] = $amount;
		}

		$value = JRequest::getVar('value', null);
		$value *= 100;

		$view	=& $this->getView('Betting', 'html', 'BettingView');

		$view->assignRef('funds', $funds);
		$view->assignRef('meeting', $meeting);
		$view->assignRef('race', $race);
		$view->assignRef('selection_list', $selection_list);
		$view->assignRef('runner_list_by_id', $runner_list_by_id);
		$view->assignRef('runner_list_by_number', $runner_list_by_number);
		$view->assignRef('event_id', $race->event_id);

		$view->assignRef('bet_type', $bet_type);
		$view->assign('value', $value);

		$view->assignRef('bet_product', $bet_product);
		$view->assign('bet_total', $bet_total);
		$view->assign('wagering_bet_list', $wagering_bet_list);

		$view->assign('flexi_flag', $data['flexi_flag']);
		$view->assign('bet_origin', $data['bet_origin']);
		
		$view->display();
	}
	
	public function saveBet()
	{
		$session	=& JFactory::getSession();
		
		//Get free bet amount - as "on" or "off"
		$free_bet_amount_input		= JRequest::getVar('chkFreeBet', 'off');		
		
		$validation	= $this->_validateBet($free_bet_amount_input);
		$redirect	= '/betting/racing';
		
		$race					= isset($validation->data['race']) ? $validation->data['race'] : null;
		$bet_type				= isset($validation->data['bet_type']) ? $validation->data['bet_type'] : null;
		$meeting				= isset($validation->data['meeting']) ? $validation->data['meeting'] : null;
		$wagering_bet_list		= isset($validation->data['wagering_bet_list']) ? $validation->data['wagering_bet_list'] : null;
		$runner_list_by_number	= isset($validation->data['runner_list_by_number']) ? $validation->data['runner_list_by_number'] : array();
		$bet_origin_keyword		= isset($validation->data['bet_origin']) ? $validation->data['bet_origin'] : 'betting';
		
		if (!is_null($meeting)) {
			$redirect .= '/meeting/' . $meeting->id;
		}
		
		if (!is_null($race)) {
			$redirect .= '/' . $race->number;
		}
		
		if ($validation->error) {
			$session->set('sess_error', $validation->error, 'betting');
			return;
		}

		$user =& JFactory::getUser();
		
		$bet_model					=& $this->getModel('Bet', 'BettingModel');
		$bet_selection_model		=& $this->getModel('BetSelection', 'BettingModel');
		$bet_result_status_model	=& $this->getModel('BetResultStatus', 'BettingModel');
		$bet_product_model			=& $this->getModel('BetProduct', 'BettingModel');
		$bet_origin_model			=& $this->getModel('BetOrigin', 'BettingModel');
		$bet_type_model 			=& $this->getModel('BetType', 'BettingModel');
		
		$unresult_status	= $bet_result_status_model->getBetResultStatusByName('unresulted');
		$refunded_status	= $bet_result_status_model->getBetResultStatusByName('fully-refunded');
		$bet_origin			= $bet_origin_model->getBetOriginByKeyword($bet_origin_keyword);
		
		//For user account
		require_once (JPATH_BASE . DS . 'components' . DS . 'com_payment' . DS . 'models' . DS . 'accounttransaction.php');
		$payment_model = new PaymentModelAccounttransaction();		
				
		$api = WageringApi::getInstance(WageringApi::API_TOB);

		foreach ($wagering_bet_list as $wagering_bet) {
				
			//how much free credit do they have left if they want to place a free bet?
			//this is checked each time for every bet
			$free_bet_amount = ($free_bet_amount_input != 'off') ? $user->tournament_dollars->getTotal() : 0;
			
			$acc_balance = $payment_model->getTotal();			
			$total_balance = $free_bet_amount + $acc_balance;
						
			$bet_freebet_transaction_id = $bet_freebet_refund_transaction_id = 0;
			
			/*
			 * Deduct the amount from the user's balances that apply
			 */ 
			 
			//free bet
			if($free_bet_amount >0) {
				if($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
					//full free bet	
					$bet_freebet_transaction_id	= $user->tournament_dollars->decrement($wagering_bet->getTotalBetAmount(), 'freebetentry'); // introducing freebet-entry keyword for transaction type
				}
				else if ($total_balance >= $wagering_bet->getTotalBetAmount()){
					//split bet with free and account credit
					$bet_freebet_transaction_id	= $user->tournament_dollars->decrement($free_bet_amount, 'freebetentry'); // introducing freebet-entry keyword for transaction type
					$bet_transaction_id	= $user->account_balance->decrement(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betentry');
				}
			}
			//normal bet				
			else if ($acc_balance >= $wagering_bet->getTotalBetAmount()){				
				$bet_transaction_id	= $user->account_balance->decrement($wagering_bet->getTotalBetAmount(), 'betentry');
			} 
			else {
				//we have run out of options to place a bet - no funds available
				$session->set('sess_error', JText::_('Insufficient funds to bet'), 'betting');
				exit;				
			}
			
			$bet_type_name	= $bet_type_model->getBetTypeByName($wagering_bet->getBetType(), true);

			$bet_product		= $bet_product_model->getBetProduct($bet_origin->id);

			// build the bet
			$bet = clone $bet_model;
			
			$bet->external_bet_id			= 0;
			$bet->user_id					= (int)$user->id;
			$bet->bet_amount				= (int)$wagering_bet->getBetAmount();
			$bet->bet_type_id				= (int)$bet_type_name->id;
			$bet->bet_result_status_id		= (int)$unresult_status->id;
			$bet->bet_origin_id				= (int)$bet_origin->id;
			$bet->bet_product_id			= (int)$bet_product->id;
			$bet->bet_transaction_id		= (int)$bet_transaction_id;
			$bet->bet_freebet_transaction_id= (int)$bet_freebet_transaction_id;
			$bet->flexi_flag				= (int)$wagering_bet->isFlexiBet() ? 1 : 0;
			
			//save freebet into the database
			if($free_bet_amount > 0) {
				$bet->bet_freebet_flag		= 1;
				if($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
					$bet->bet_freebet_amount	= (float)$wagering_bet->getTotalBetAmount();
				} else {
					$bet->bet_freebet_amount	= (float)$free_bet_amount;
				}
			}
						
			$bet_id = $bet->save();
			
			/*
			 * problem saving bet - refund the amounts to accounts that apply
			 */ 
			if (!$bet_id) {
				if($free_bet_amount >0) {
					if($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
						$user->tournament_dollars->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund'); // introducing freebetrefund keyword for transaction type
					}
					else {
						$user->tournament_dollars->increment($free_bet_amount, 'freebetrefund'); // introducing freebetrefund keyword for transaction type
						$user->account_balance->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund'); 						
					}
				}
				else $user->account_balance->increment($wagering_bet->getTotalBetAmount(), 'betrefund');
				
				$session->set('sess_error', JText::_('Cannot place this bet'), 'betting');
				exit;
			}
			
			$bet->id = $bet_id;
			
			$bet_selection_list = $wagering_bet->getBetSelectionList();
			
			/*
			 *  save our selections to the database for this bet ticket
			 */ 
			foreach ($bet_selection_list as $pos => $bet_selection) {
				
				if (!is_array($bet_selection)) {
					$bet_selection = array($bet_selection);
				}
				
				foreach ($bet_selection as $runner_number) {
				
					$selection = clone $bet_selection_model;
					
					$selection->bet_id			= (int)$bet_id;
					$selection->selection_id	= (int)$runner_list_by_number[$runner_number]->id;
					$selection->position		= ($wagering_bet->isStandardBetType() || $wagering_bet->isBoxed()) ? 0 : (int)$pos;
					
					//problem saving selection for this bet ticket - do a refund
					if (!$selection->save()) {
						if($free_bet_amount > 0) {
						//refund the bet to the correct account(s)
							if($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
								$bet_freebet_refund_transaction_id	= $user->tournament_dollars->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund'); 
							}
							else {
								$bet_freebet_refund_transaction_id	= $user->tournament_dollars->increment($free_bet_amount, 'freebetrefund'); 
								$bet_refund_transaction_id	= $user->account_balance->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund'); 
							}
						}
						else $bet_refund_transaction_id	= $user->account_balance->increment($wagering_bet->getTotalBetAmount(), 'betrefund');
					
						$bet->refund_transaction_id	= (int)$bet_refund_transaction_id;
						$bet->refund_freebet_transaction_id	= (int)$bet_freebet_refund_transaction_id;
						$bet->resulted_flag			= 1;
						$bet->refunded_flag			= 1;
						$bet->bet_result_status_id	= (int)$refunded_status->id;
						$bet->save();
						$session->set('sess_error', JText::_('Cannot store bet selections'), 'betting');
						exit;
					}
				}
			}
			
			$api_error		= null;
			$bet_confirmed	= false;
			
			/*
			 * This places the bet with the external 3rd party
			 * First checks if Norfolk Island is available
			 */ 
			if ($this->confirmAcceptance($bet_id, $user->id, 'bet', time()+600)) {
					
				//BET PLACED VIA API	
				$external_bet	= $api->placeBet($wagering_bet, $meeting, $bet_id);
				$api_error		= $api->getErrorList(true);
				
				if (empty($api_error) && isset($external_bet->wagerId)) {
					$bet_confirmed	= true;
					$bet->external_bet_id = $bet_id;//(int)$external_bet->wagerId;
					$bet->invoice_id = $external_bet->wagerId;
							
					// Set the bet_status based on $external_bet->status
					$bet_status = 5;
					if($external_bet->status == "N" || $external_bet->status == "E")
					{
						$bet_status = 5;
					}elseif($external_bet->status == "S" || $external_bet->status == "W" || $external_bet->status == "L"){
						$bet_status = 1;
					}elseif($external_bet->status == "F" || $external_bet->status == "CN"){
						$bet_status = 4;
						$bet_confirmed	= false;
					}
					
					$bet->bet_result_status_id = (int)$bet_status;
					$bet->save();
				}else{
						$bet->external_bet_error_message = (string)$api_error;
					}
			}
		
			/*
			 * problem placing bet via 3rd party - refund the amounts to accounts that apply
			 */ 		
			if (!$bet_confirmed) {
				
				if($free_bet_amount > 0) {
					//refund the bet to the correct account(s)
					if($free_bet_amount >= $wagering_bet->getTotalBetAmount()) {
						$bet_freebet_refund_transaction_id	= $user->tournament_dollars->increment($wagering_bet->getTotalBetAmount(), 'freebetrefund'); 
					}
					else {
						$bet_freebet_refund_transaction_id	= $user->tournament_dollars->increment($free_bet_amount, 'freebetrefund');
						$bet_refund_transaction_id	= $user->account_balance->increment(($wagering_bet->getTotalBetAmount() - $free_bet_amount), 'betrefund'); 
					}
				}
				else $bet_refund_transaction_id	= $user->account_balance->increment($wagering_bet->getTotalBetAmount(), 'betrefund');
				
				$bet->refund_transaction_id	= (int)$bet_refund_transaction_id;
				$bet->refund_freebet_transaction_id	= (int)$bet_freebet_refund_transaction_id;
				$bet->resulted_flag			= 1;
				$bet->refunded_flag			= 1;
				$bet->bet_result_status_id	= (int)$refunded_status->id;
				$bet->save();
				
				$this->confirmAcceptance($bet_id, $user->id, 'beterror', time()+600);
				
				
				$session->set('sess_error', JText::_(empty($api_error) ? 'Bet could not be registered' : $api_error), 'betting');
				exit;
			}
		}

		//*** IF WE GET HERE - EVERYTHING WENT WELL ***
		$session->set('sess_message', JText::_('Your bet has been placed'), 'betting');
		exit;
	}
	
	private function _loadUnserializeRequiredClasses($bet_type_name)
	{
		if (!class_exists('TournamentModelMeeting')) {
			JLoader::import( 'meeting', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		
		if (!class_exists('TournamentModelRace')) {
			JLoader::import( 'race', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		
		if (!class_exists('TournamentModelRunner')) {
			JLoader::import( 'runner', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
		}
		
		if (!class_exists('BettingModelBetType')) {
			JLoader::import( 'bettype', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
		}
		
		if ($this->_isExoticBetType($bet_type_name) && !class_exists('WageringBetExotic' . $bet_type_name)) {
			JLoader::import('mobileactive.wagering.bet.exotic.' . $bet_type_name);
		}
	}
	
	
	public function bettingHistory()
	{
		global $mainframe, $option;
		
		$bet_model					=& $this->getModel('Bet', 'BettingModel');
		$bet_selection_model		=& $this->getModel('BetSelection', 'BettingModel');
		$bet_result_status_model	=& $this->getModel('BetResultStatus', 'BettingModel');
		$bet_product_model			=& $this->getModel('BetProduct', 'BettingModel');
		$bet_origin_model			=& $this->getModel('BetOrigin', 'BettingModel');

		$user =& JFactory::getUser();
		
		$result_type	= JRequest::getVar('result_type', null);
		
		$lists = array();
		
		$filter_from_date	= $mainframe->getUserStateFromRequest($option.'filter_history_from_date', 'filter_history_from_date');
		$filter_to_date		= $mainframe->getUserStateFromRequest($option.'filter_history_to_date', 'filter_history_to_date');

		$lists['from_date']	= $filter_from_date;
		$lists['to_date']	= $filter_to_date;
		
		$filter = array(
			'user_id'		=> $user->id,
			'result_type'	=> $result_type,
			'from_time'		=> $filter_from_date ? strtotime($filter_from_date) : null,
			'to_time'		=> $filter_to_date ? (strtotime($filter_to_date) + 24 * 60 * 60) : null,
		);
		
		$offset = $mainframe->getUserStateFromRequest(
			JRequest::getVar('limitstart', 0, '', 'int'),
			'limitstart',
			0
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
		$view->assign('result_type', $result_type);
		
		$bet_selection_model =& $this->getModel('BetSelection', 'BettingModel');
		$view->setModel($bet_selection_model);
		
		$selection_result_model =& $this->getModel('SelectionResult', 'TournamentModel');
		$view->setModel($selection_result_model);
		
		$meeting_model =& $this->getModel('Meeting', 'TournamentModel');
		$view->setModel($meeting_model);
		
		$view->display();
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
	 * Check Bet limit
	 *
	 * @return boolean
	 */
	protected function _checkBetLimit($bet_total)
	{
		$user =& JFactory::getUser();
		$user_model =& $this->getModel('TopbettaUser', 'TopbettaUserModel');
		$user_data = $user_model->getUser($user->id);
		
		if ($user_data->bet_limit != -1) {
			$from_time		= strtotime(date('Y-m-d'));
			$today_betting	= $user->account_balance->getTotalAmountByTransactionType('betentry', $user->id, $from_time);
			$today_winning	= $user->account_balance->getTotalAmountByTransactionType('betwin', $user->id, $from_time);
			$today_refund	= $user->account_balance->getTotalAmountByTransactionType('betrefund', $user->id, $from_time);
			
			$today_tournament_entry = $user->account_balance->getTotalAmountByTransactionType('entry', $user->id, $from_time);
			$today_tournament_buyin = $user->account_balance->getTotalAmountByTransactionType('buyin', $user->id, $from_time);
			$today_tournament_win	= $user->account_balance->getTotalAmountByTransactionType('tournamentwin', $user->id, $from_time);
			
			$total_winning	= $today_winning + $today_refund + $today_tournament_win;
			$total_spending	= abs($today_betting + $today_tournament_entry + $today_tournament_buyin);
			
			if (($user_data->bet_limit + $total_winning - $bet_total - $total_spending) < 0) {
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Print bet content
	 *
	 * @return boolean
	 */
	public function printBet()
	{
		
		$title		= JRequest::getVar('title', null, 'post');
		$content	= JRequest::getVar('content', null, 'post', 'none', 2);
		
		$view	=& $this->getView('Betting', 'html', 'BettingView');
		$view->setLayout('printbet');
		
		$view->assign('title', $title);
		$view->assign('content', $content);
		
		$view->display();
	}
	
	/**
	 * Show open torunament tickets content
	 *
	 */
	public function showOpenTournamentsForTopMenu()
	{
		$user =& JFactory::getUser();
		
		// Include the syndicate functions only once
		require_once (JPATH_BASE . DS . 'modules' . DS . 'mod_bslogin'.DS.'helper.php');
		
		if(!$user->guest) {
			if(!class_exists('TournamentModelTournament')) {
				JLoader::import( 'tournament', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentTicket')) {
				JLoader::import( 'tournamentticket', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentRacing')) {
				JLoader::import( 'tournamentracing', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentSport')) {
				JLoader::import( 'tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentLeaderboard')) {
				JLoader::import( 'tournamentleaderboard', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentSportEvent')) {
				JLoader::import( 'tournamentsportevent', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}			
			
		
			$ticket_model = JModel::getInstance( 'TournamentTicket', 'TournamentModel' );
		
			$tournament_model	= JModel::getInstance('Tournament', 'TournamentModel');
			$racing_model		= JModel::getInstance('TournamentRacing', 'TournamentModel');
			$leaderboard_model	= JModel::getInstance('TournamentLeaderboard', 'TournamentModel');
		
			$tickets_open = array();
		
			$open_ticket_list = $ticket_model->getTournamentTicketActiveListByUserID($user->id);
		
			$tournament_sport_model = JModel::getInstance('TournamentSport', 'TournamentModel');
			$racing_sports			= $tournament_sport_model->excludeSports;
		
			$tournament_sport_event_model	= JModel::getInstance('TournamentSportEvent', 'TournamentModel');
		
			foreach($open_ticket_list as $ticket) {
				$tournament			= $tournament_model->getTournament($ticket->tournament_id);
				$tournament_sport	= $tournament_sport_model->getTournamentSport($tournament->tournament_sport_id);
				$bet_open			= strtotime($tournament->end_date) > time();
				$tournament_type	= in_array($tournament_sport->name, $racing_sports) ? 'racing' : 'sports';
				if('sports' == $tournament_type && $bet_open) {
					$sport_tournament	= $tournament_sport_event_model->getTournamentSportEventByTournamentID($ticket->tournament_id);
					$bet_open			= strtotime($sport_tournament->betting_closed_date) > time();
				}
		
				$icon_image = modbsLoginHelper::getTournamentIcon(preg_replace('/[^a-z0-9]/i', '', strtolower($tournament_sport->name)));
		
				$tickets_open[$ticket->sport_name][$ticket->id] = array(
					'ticket_id'			=> $ticket->id,
					'icon'				=> $icon_image,
					'buy_in'			=> $ticket->buy_in > 0 ? ('$' . number_format($ticket->buy_in / 100, 2)) : 'Free',
					'tournament_name'	=> $ticket->tournament_name,
					'tournament_id'		=> $ticket->tournament_id,
					'bet_open_txt'		=> $tournament->cancelled_flag ? 'Cancelled' : ($bet_open ? 'BETTING OPEN' : 'BETTING CLOSED'),
					'bet_open_class'	=> ($bet_open && !$tournament->cancelled_flag) ? 'betting-open' : 'betting-closed',
					'qualified_txt'		=> $tournament->cancelled_flag ? 'Cancelled' : 'Pending',
					'qualified_class'	=> 'ticket-pending',
					'leaderboard_rank'	=> 'N/A',
					'betta_bucks'		=> '$' . number_format($ticket_model->getAvailableTicketCurrency($ticket->tournament_id, $user->id) / 100, 2),
					'tournament_type'	=> $tournament_type,
				);
		
				$leaderboard = $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);
		
				if($leaderboard && !$tournament->cancelled_flag) 
					{
					$tickets_open[$ticket->sport_name][$ticket->id]['qualified_txt'] = ($leaderboard->qualified ? 'Qualified' : 'Pending');
					$tickets_open[$ticket->sport_name][$ticket->id]['qualified_class'] = ($leaderboard->qualified ? 'ticket-qualified' : 'ticket-pending');
					$tickets_open[$ticket->sport_name][$ticket->id]['leaderboard_rank'] = ($leaderboard->rank == '-' ? 'N/Q' : $leaderboard->rank);
					$tickets_open[$ticket->sport_name][$ticket->id]['betta_bucks'] = '$' . number_format($ticket_model->getAvailableTicketCurrency($tournament->id, $user->id)/100, 2);
					}
					
				}
			
			$ticket_button_class = (empty($tickets_open) ? ' class="inactive"' : '');
		}

		$view	=& $this->getView('Betting', 'html', 'BettingView');
		$view->setLayout('my-open-tournaments');
		
		$view->assign('tickets_open', $tickets_open);
		
		$view->display();
	}
	
	public function showRecentTournamentsForTopMenu()
	{
		// Include the syndicate functions only once
		require_once (JPATH_BASE . DS . 'modules' . DS . 'mod_bslogin'.DS.'helper.php');
		
		$user =& JFactory::getUser();
		if(!$user->guest) {
			if(!class_exists('TournamentModelTournament')) {
				JLoader::import( 'tournament', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentTicket')) {
				JLoader::import( 'tournamentticket', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentRacing')) {
				JLoader::import( 'tournamentracing', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentSport')) {
				JLoader::import( 'tournamentsport', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentLeaderboard')) {
				JLoader::import( 'tournamentleaderboard', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}
		
			if(!class_exists('TournamentModelTournamentSportEvent')) {
				JLoader::import( 'tournamentsportevent', JPATH_BASE . DS . 'components' . DS . 'com_tournament' . DS . 'models' );
			}			
					
			$ticket_model = JModel::getInstance( 'TournamentTicket', 'TournamentModel' );
		
			$tournament_model	= JModel::getInstance('Tournament', 'TournamentModel');
			$racing_model		= JModel::getInstance('TournamentRacing', 'TournamentModel');
			$leaderboard_model	= JModel::getInstance('TournamentLeaderboard', 'TournamentModel');
				
			$tickets_recent = array();
			$recent_ticket_list = $ticket_model->getTournamentTicketRecentListByUserID($user->id, time() - 48 * 60 * 60, time(), 1, 't.end_date DESC, t.start_date DESC');
			
			$tournament_sport_model = JModel::getInstance('TournamentSport', 'TournamentModel');
			$racing_sports			= $tournament_sport_model->excludeSports;
		
			$tournament_sport_event_model	= JModel::getInstance('TournamentSportEvent', 'TournamentModel');
						
			foreach($recent_ticket_list as $ticket) {
				$tournament			= $tournament_model->getTournament($ticket->tournament_id);
				$tournament_sport	= $tournament_sport_model->getTournamentSport($tournament->tournament_sport_id);
				$bet_open			= strtotime($tournament->end_date) > time();
				$tournament_type	= in_array($tournament_sport->name, $racing_sports) ? 'racing' : 'sports';
		
				$icon_image = modbsLoginHelper::getTournamentIcon(preg_replace('/[^a-z0-9]/i', '', strtolower($tournament_sport->name)));
		
				$tickets_recent[$ticket->sport_name][$ticket->id] = array(
					'ticket_id'			=> $ticket->id,
					'icon'				=> $icon_image,
					'buy_in'			=> $ticket->buy_in > 0 ? ('$' . number_format($ticket->buy_in / 100, 2)) : 'Free',
					'tournament_name'	=> $ticket->tournament_name,
					'tournament_id'		=> $ticket->tournament_id,
					'bet_open_txt'		=> $tournament->cancelled_flag ? 'CANCELLED' : 'COMPLETED',
					'bet_open_class'	=> 'betting-completed',
					'qualified_txt'		=> 'All Paying',
					'qualified_class'	=> 'ticket-qualified',
					'leaderboard_rank'	=> 'N/A',
					'tournament_type'	=> $tournament_type,
				);
		
				$prize = 0;
				if(!$ticket->cancelled_flag && $ticket->result_transaction_id) {
					if($ticket->jackpot_flag) {
						$transaction_record =  $user->tournament_dollars->getTournamentTransaction($ticket->result_transaction_id);
					} elseif($tournament->free_credit_flag) {
						$transaction_record =  $user->tournament_dollars->getTournamentTransaction($ticket->result_transaction_id);
					}else {
						$transaction_record =  $user->account_balance->getAccountTransaction($ticket->result_transaction_id);
					}
					if($transaction_record && $transaction_record->amount > 0 ) {
						$prize = $transaction_record->amount;
					}
				}
			
				$tickets_recent[$ticket->sport_name][$ticket->id]['prize'] = ('$' . number_format($prize / 100, 2) );
				
				$tickets_recent[$ticket->sport_name][$ticket->id]['winner_alert_flag'] = $ticket->winner_alert_flag;
		
				$leaderboard = $leaderboard_model->getLeaderBoardRankByUserAndTournament($user->id, $tournament);
				if($leaderboard) {
					$tickets_recent[$ticket->sport_name][$ticket->id]['leaderboard_rank'] = ($leaderboard->rank == '-' ? 'N/Q' : $leaderboard->rank);
					
					//if($leaderboard->rank == 1)
					//{
						$ticket_model->setWinnerAlertFlagByTournamentID($ticket->tournament_id);
					//}
				}
							
			}
		}
		$view	=& $this->getView('Betting', 'html', 'BettingView');
		$view->setLayout('my-recent-tournaments');
		
		$view->assign('tickets_recent', $tickets_recent);
		
		$view->display();
	}
	
	public function showUnresultedBetsForTopMenu()
	{
		// Include the syndicate functions only once
		require_once (JPATH_BASE . DS . 'modules' . DS . 'mod_bslogin'.DS.'helper.php');
		
		$user =& JFactory::getUser();
		if(!$user->guest) {	
				
			if(!class_exists('BettingModelBet')) {
				JLoader::import( 'bet', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
			}
				
			
			$bet_model				= JModel::getInstance('Bet', 'BettingModel');
			$unresulted_bet_list	= $bet_model->getActiveBetListByUserID($user->id);
			$bets_unresulted		= modbsLoginHelper::getBetDisplayList($unresulted_bet_list);
		}
		
		$view	=& $this->getView('Betting', 'html', 'BettingView');
		$view->setLayout('my-unresulted-bets');
		
		$view->assign('bets_unresulted', $bets_unresulted);
		$view->display();	
	}
	
	public function showRecentBetsForTopMenu()
	{
		// Include the syndicate functions only once
		require_once (JPATH_BASE . DS . 'modules' . DS . 'mod_bslogin'.DS.'helper.php');
		
		$user =& JFactory::getUser();
		if(!$user->guest) {	
			if(!class_exists('BettingModelBet')) {
				JLoader::import( 'bet', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models' );
			}
				
			$bet_model				= JModel::getInstance('Bet', 'BettingModel');
			$recent_bet_list	= $bet_model->getBetRecentListByUserID($user->id, time() - 48 * 60 * 60, time(), 1, 'e.start_date DESC');
			$bets_recent		= modbsLoginHelper::getBetDisplayList($recent_bet_list, true);
				
			$bet_button_class	= (empty($bets_unresulted) ? ' class="inactive"' : '');
		}
		
		$view	=& $this->getView('Betting', 'html', 'BettingView');
		$view->setLayout('my-recent-bets');
		
		$view->assign('bets_recent', $bets_recent);	
		$view->display();	
	}
	
	public function showAllBalanceForTopMenu()
	{
		
		$user =& JFactory::getUser();
		if(!$user->guest) {	
					
		$balances = array('account_balance', 'tournament_dollars');
			foreach($balances as $balance) {
				$amount = 0;
				if(!empty($user->$balance)) {
					$user->$balance->setUserId($user->get('id'));
					$amount = $user->$balance->getTotal();
					if(!empty($amount)) {
						$amount = $amount / 100;
					}
				}
				$funds[$balance] = '$ '.number_format($amount, 2, '.', ',');
			}
		}
		$view	=& $this->getView('Betting', 'html', 'BettingView');
		$view->setLayout('my-balance');
		
		$view->assign('tournament_dollars', $funds['tournament_dollars']);	
		$view->assign('account_balance', $funds['account_balance']);	
		$view->display();
	}
}
