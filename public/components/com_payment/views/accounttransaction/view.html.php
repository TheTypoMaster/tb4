<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: view.html.php 2010-08-08 23:27:25 svn $
 * @author Fei Sun
 * @package Joomla
 * @subpackage payment
 * @license GNU/GPL
 *
 * This is payment component
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('mobileactive.application.component.view');

/**
 * HTML View class for the payment component
 */
class PaymentViewAccounttransaction extends View
{
	public function display($tpl = null)
	{
		$layout	= JRequest::getVar('layout', 'default');
		switch ($layout) {
			case 'transactions':
				$this->transactions();
				break;
			case 'instantdeposit':
				$this->instantdeposit();
			break;
		}
		$document = & JFactory::getDocument();
	    $document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');

	    parent::display($tpl);
	}
	
	public function transactions()
	{
		$transaction_display_list = array();
		
		foreach ($this->transaction_list as $transaction) {
			$sport = in_array($transaction->sport_name, $this->racing_sport_list) ? 'racing' : 'sports';
			
			if ($transaction->tournament && !$transaction->ticket_refunded_flag) {
				$description = $transaction->tournament;
			} else if ($transaction->bet_entry_id || $transaction->bet_win_id) {
				$bet_model		=& $this->getModel('Bet');
				$meeting_model	=& $this->getModel('Meeting');
				$race_model		=& $this->getModel('Race');
				$runner_model	=& $this->getModel('Runner');
				
				$wagering_bet	= WageringBet::newBet();
				$bet_id			= ($transaction->bet_entry_id ? $transaction->bet_entry_id : $transaction->bet_win_id);
				$bet			= $bet_model->getBetDetails($bet_id);
				$label			= BettingHelper::getBetTicketDisplay($bet_id);
				$description	= $label . ' ' . $wagering_bet->getBetTypeDisplayName($bet->bet_type) . ' (Ticket: ' . $bet_id . ')';
			} else {
				$description = $transaction->description;
			}
			
			$link = null;
			if ($transaction->tournament_id && !$transaction->ticket_refunded_flag) {
				$link = '/tournament/' . $sport . '/game/' . $transaction->tournament_id;
			}
			
			$transaction_display_list[$transaction->id] = array(
				'time'			=> $transaction->created_date,
				'link'			=> $link,
				'description'	=> $description,
				'amount_class'	=> $transaction->amount < 0 ? 'negative' : 'positive',
				'amount'		=> Format::currency(abs($transaction->amount)),
				'type'			=> $transaction->type,
			);
			
			if ($transaction->amount > 0) {
				$transaction_display_list[$transaction->id]['type'] = 'Deposit - ' . $transaction_display_list[$transaction->id]['type'];
			} else if ($transaction->amount < 0) {
				$transaction_display_list[$transaction->id]['type'] = 'Withdrawal - ' . $transaction_display_list[$transaction->id]['type'];
			}
		}
		
		$nav_list	= array(
			'all'					=> 'All Transactions',
			'deposits_withdrawals'	=> 'Deposits/Withdrawal',
			'bets'					=> 'Bets',
			'tournaments'			=> 'Tournaments',
		);
		$current_nav = isset($nav_list[$this->transaction_type]) ? $this->transaction_type : 'all';
		$this->assign('transaction_display_list', $transaction_display_list);
		
		$this->assign('nav_list', $nav_list);
		$this->assign('current_nav', $current_nav);
		$this->assign('current_date', date('d / m / Y'));
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TopBetta - Account Transactions'));
		//Add stylesheets to the document
		$document->addStyleSheet('components/com_payment/assets/accounttransaction.default.css');
		
		$document->addScript('/media/system/js/datepicker.js' );
			
		$js = "window.addEvent('domready', function(){
			$$('input.DatePicker').each( function(el){
			new DatePicker(el);
			});
		}); ";
		$document->addScriptDeclaration($js);
		
		$css = '/media/system/css/datepicker.css';
		$document->addStyleSheet($css);
		
    	$this->setLayout('transactions');
	}
	
	public function instantdeposit()
	{
		$user =& JFactory::getUser();
		
		$userPin		= sprintf("%07d",$user->id);
		$this->bpayRef	= $userPin . $this->_mod10($userPin);
		$this->user		= $user;
		
		$document = & JFactory::getDocument();
		$document->setTitle( JText::_('TopBetta - Deposit') );
		
		$this->setLayout('instantdeposit');
	}
  
	private function _mod10($seedval)
	{
		$mysum = 0;
		for ($x=0; $x<strlen($seedval); $x++) {
			$digit = substr($seedval, $x, 1);
			if (strlen($seedval) % 2 == 1) {
        		//to multiply by 2 and then by 1
				if ($x/2 == floor($x/2)) $digit *= 2;
				// end multiplicaton
			} else {
				//to multiple first by 1 and then by 2
				if ($x/2 == floor($x/2)) {
					$digit *= 1;
				} else {
					$digit *= 2;
				} //end multiplication
			}
			if (strlen($digit) == 2) $digit = substr($digit, 0, 1) + substr($digit, 1, 1);
			$mysum += $digit;
		}
		$rem = $mysum % 10;
		//if remainder is string, just a way to convert to integer by adding 0
		$rem = $rem + 0;
		if ($rem == 0) {
			$checkdigit = 0;
		} else {
			$checkdigit = 10 - $rem;
		}
		return $checkdigit;
	}
}
?>