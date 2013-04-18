<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('mobileactive.application.component.view');

class BettingViewBetting extends View
{
	/**
	* Method to diplay the view
	* 
	* @return void
	*/
	public function display($tpl = null)
	{
		$task = JRequest::getVar('task', 'list');

		switch ($task) {
			case 'list':
			default:
				$this->listBets();
				break;
		}

		parent::display($tpl);
	}
	
	/**
	* Method to diplay the view
	* 
	* @return void
	*/
	function listBets()
	{
		$bet_display_list = array();
		
		$bet_selection_model	=& $this->getModel('BetSelection');
		$selection_result_model	=& $this->getModel('SelectionResult');
		
		$wagering_bet = WageringBet::newBet();
		
		$i = 0;
		foreach ($this->bet_list as $bet) {
			$label = BettingHelper::getBetTicketDisplay($bet->id);
			
			$bet_display_list[$bet->id] = array(
				'external_bet_id'	=> empty($bet->external_bet_id) ? '&mdash;' : $bet->external_bet_id,
				'row_class'			=> 'row' . $i % 2,
				'username'			=> $bet->username,
				'bet_time'			=> $bet->created_date,
				'label'				=> $label,
				'bet_type'			=> $wagering_bet->getBetTypeDisplayName($bet->bet_type),
				'amount'			=> FORMAT::currency($bet->bet_amount),
				'bet_total'			=> FORMAT::currency(abs($bet->bet_total)),
				'dividend'			=> '&mdash;',
				'paid'				=> '&mdash;',
				'result'			=> '&mdash;',
				'half_refund'		=> false,       
			);
			
			if ($bet->refunded_flag && !$bet->win_amount) {
				$bet_display_list[$bet->id]['result']	= 'REFUNDED';
				if ($bet->refund_amount > 0) {
					$bet_display_list[$bet->id]['paid']	= Format::currency($bet->refund_amount);  
				}
			} else if ($bet->resulted_flag && empty($bet->win_amount)) {
				$bet_display_list[$bet->id]['result']	= 'LOSS';
				$bet_display_list[$bet->id]['paid']		= 'NIL';
			} else if ($bet->resulted_flag) {
				$bet_display_list[$bet->id]['result']	= 'WIN';
				$bet_display_list[$bet->id]['paid']		= Format::currency($bet->win_amount);
				
				if ($wagering_bet->isStandardBetType($bet->bet_type)) {
					$selection_result	= $selection_result_model->getSelectionResultBySelectionID($bet->selection_id);
					$win_dividend		= $selection_result->win_dividend;
					$place_dividend		= $selection_result->place_dividend;
					
					switch ($bet->bet_type) {
						case WageringBet::BET_TYPE_WIN:
							$bet_display_list[$bet->id]['dividend'] = Format::odds($win_dividend);
							break;
						case WageringBet::BET_TYPE_PLACE:
							$bet_display_list[$bet->id]['dividend'] = Format::odds($place_dividend);
							break;
						case WageringBet::BET_TYPE_EACHWAY:
							$bet_display_list[$bet->id]['dividend']  = Format::odds($win_dividend);
							$bet_display_list[$bet->id]['dividend'] .= '/';
							$bet_display_list[$bet->id]['dividend'] .= Format::odds($place_dividend);
							break;
					}
				} else {
					$bet_dividends = unserialize($bet->{$bet->bet_type . '_dividend'});
					
					$bet_display_list[$bet->id]['dividend'] = '&mdash;';
					$dividends_count = count($bet_dividends);
					
					if ($dividends_count == 1) {
						$bet_display_list[$bet->id]['dividend'] = Format::odds(array_shift($bet_dividends));
					} else if ($dividends_count > 1) {
						$bet_display_list[$bet->id]['dividend'] = array();
						foreach ($bet_dividends as $combination => $bet_dividend) {
							$bet_display_list[$bet->id]['dividend'][] = $combination . ': ' . Format::odds($bet_dividend); 
						}
						$bet_display_list[$bet->id]['dividend'] = implode('<br />', $bet_display_list[$bet->id]['dividend']);
					}
				}
				
				if ($bet->refunded_flag) {
					$scrached_list = $bet_selection_model->getBetSelectionListByBetIDAndSelectionStatus($bet->id, 'scratched');
					$scrached_display = array();
					foreach ($scrached_list as $scrached) {
						$scrached_display[] = $scrached->number . '. ' . $scrached->name;
					}
					
					$bet_display_list[$bet->id]['half_refund'] = array(
						'label'		=> implode(', ', $scrached_display),
						'bet_type'	=> $wagering_bet->getBetTypeDisplayName($bet->bet_type),
						'amount'	=> '&mdash;',
						'bet_total'	=> '&mdash;',
						'dividend'	=> '&mdash;',
						'paid'		=> Format::currency($bet->refund_amount),
						'result'	=> 'REFUND'
					);
				}
			}
			$i++;
		}
		
		$this->assign('bet_display_list', $bet_display_list);
		
		$bet_result_type_list = array(
			''			=> 'All Bets',
			'winning'	=> 'Winning',
			'losing'	=> 'Losing',
			'refunded'	=> 'Refunded',
		);
		$this->assign('bet_result_type_list', $bet_result_type_list);
		
    	// Build the toolbar for the add function
		JToolBarHelper::title(JText::_('Betting &mdash; View Bets'));
		
		$document =& JFactory::getDocument();
		$document->addScript('/media/system/js/datepicker.js' );
		$js = "window.addEvent('domready', function(){
				$$('input.DatePicker').each( function(el){
				new DatePicker(el);
			});
		}); ";
		$document->addScriptDeclaration($js);
		
		$js = "function isNumberKey(evt)
		      {
		         var charCode = (evt.which) ? evt.which : event.keyCode
		         if (charCode > 31 && (charCode < 48 || charCode > 57))
		            return false;
		         return true;
		      }
		";
		$document->addScriptDeclaration($js);
		
		$document->addStyleSheet('/media/system/css/datepicker.css');
		
		$this->setLayout('list');
    }
}
?>