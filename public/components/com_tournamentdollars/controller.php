<?php
/**
 * Joomla! 1.5 component payment
 *
 * @version $Id: controller.php 2010-08-08 23:27:25 svn $
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

jimport('joomla.application.component.controller');
jimport('mobileactive.wagering.bet');
require_once( JPATH_BASE . DS . 'components' . DS . 'com_betting'.DS.'helpers'.DS.'helper.php' );
/**
 * payment Controller
 * 
 */
class TournamentdollarsController extends JController
{
    /**
     * Constructor
     * 
     * @return void
     */
    function __construct()
    {
    	$authenticate = array(
			'display'
		);
    	
		$user	=& JFactory::getUser();
    	$task	= JRequest::getVar('task', 'display');
    	
        parent::__construct();
        
    	if ($user->guest && in_array($task, $authenticate)) {
      		$msg = JText::_("You need to login to access this part of the site.");
			$this->setRedirect('/user/register', $msg, 'error');
			$this->redirect();
		}
    }
    
    
    /**
	* Method to diplay the form
	*
	* @return Boolean true on success
	*/
    function display()
    {
    	$view	= JRequest::getVar('view', 'default');
    	$layout	= JRequest::getVar('layout', 'default');
    	$view	=& $this->getView( $view, 'html');
    	
    	$model					=& $this->getModel('tournamenttransaction');
    	$tournament_sport_model	=& $this->getModel('TournamentSport', 'TournamentModel');
    	$racing_sports			= $tournament_sport_model->excludeSports;

    	$view->setModel($model, true);
    	$view->setLayout($layout);
    	$user =& JFactory::getUser();
    	switch ($layout) {
    		case 'default':
    			$transactions = $model->listTransactions();
				
				$transaction_display_list = array();
		
				foreach ($transactions as $transaction) {
					
					if ($transaction->bet_entry_id || $transaction->bet_win_id) {
						
						if (!class_exists('BettingModelBet')) {
							JLoader::import('bet', JPATH_BASE . DS . 'components' . DS . 'com_betting' . DS . 'models');
						}
						
						$bet_model =& $this->getModel('Bet', 'BettingModel');
						
						$wagering_bet	= WageringBet::newBet();
						$bet_id			= ($transaction->bet_entry_id ? $transaction->bet_entry_id : $transaction->bet_win_id);
						$bet			= $bet_model->getBetDetails($bet_id);
						$label			= BettingHelper::getBetTicketDisplay($bet_id);
						$transaction_display_list[$transaction->bet_entry_id][$transaction->id]	= $label . ' ' . $wagering_bet->getBetTypeDisplayName($bet->bet_type) . ' (Ticket: ' . $bet_id . ')';
					} 
					
				}
    			
    			$page =& $model->getPagination();
    			$view->assignRef('transactions', $transactions);
				$view->assignRef('transactions_description', $transaction_display_list);
    			$view->assignRef('racing_sports', $racing_sports);
				$view->assignRef('page', $page);
    			break;
    	}
    	
    	$view->assign('itemid', JRequest::getVar('Itemid'));
    	
    	$view->display();
    }
    
	
}
?>
