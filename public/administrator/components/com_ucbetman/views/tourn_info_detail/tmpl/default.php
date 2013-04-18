<?php
/**
 * @version $Id: component.php 5173 2006-09-25 18:12:39Z Jinx $
 * @package Joomla
 * @subpackage Config
 * @copyright Copyright (C) 2005 - 2006 Open Source Matters. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 * 
 * php echo $lang->getName();  
 */
defined('_JEXEC') or die('Restricted access');

//DEVNOTE: import html tooltips
JHTML::_('behavior.tooltip');

$nowTime = strtotime('now');
if ($this->detail->paid == "0"){
	$tournPaid = "Tournament Complete";
}else {
	$tournPaid = "Tournament Running";
}
?>

<script language="javascript" type="text/javascript">
	
	window.addEvent('domready', function(){

		// cancel tourament button event
		$('cancel_tournament').addEvent('click', function(){
			var tournID = $("tournament_id").value;
			
			var answer = confirm("Do you want to cancel this Tournament and Refund clinets? ")
			if (answer) {
				var closeText = prompt("Please enter reason");
				var redirURL = "index.php?option=com_ucbetman&controller=tourn_info_detail&task=closeTournament&tournID="+ tournID+"&closeText="+ closeText;
				window.location = redirURL;
			}else{
				return;
			}
		});

		// catch clicks on the stop race bets buttons
		$$(".stop_bets").each(function(el){
			el.addEvent('click', function(listID){
				
				var answer = confirm("Do you want to stop bets for the selected race? " +listID)
				if (answer) {
					var redirURL = "index.php?option=com_ucbetman&controller=tourn_info_detail&task=stopRaceBets&raceID="+listID;
					window.location = redirURL;
				}else{
					return;
				}
			}.pass(el.id)
			);
		});

	});
	
</script>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Tournament Information' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournament_id">
					<?php echo JText::_( 'Tournament ID' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="tournament_id" id="tournament_id" size="24" maxlength="24" value="<?php echo $this->detail->tournID; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="cancel_tournament">
					<?php echo JText::_( 'Cancel Tournament & Refund Bets:' ); ?>:
				</label>
			</td>
			<td><button id="cancel_tournament", name="cancel_tournament">Cancel Tournament</button></td>
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournName">
					<?php echo JText::_( 'Tournament Name' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="tournName" id="tournName" size="32" maxlength="250" value="<?php echo $this->detail->tournament_name; ?>" />
			</td>
			
		</tr>	
		<tr>
			
		<td width="100" align="right" class="key">
				<label for="tab_meeting_id">
					<?php echo JText::_( 'TAB Meeting Code' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="meetingCode" id="meetingCode" size="32" maxlength="250" value="<?php echo $this->detail->tab_meeting_id; ?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="track">
					<?php echo JText::_( 'Tournament Value' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="meetingCode" id="meetingCode" size="48" maxlength="250" value="<?php echo 'BuyIn: $'.$this->detail->tournament_value .' + Entry Fee: $'.$this->detail->entryFee ; ?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="track">
					<?php echo JText::_( 'Game Play' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="meetingCode" id="meetingCode" size="12" maxlength="250" value="<?php echo $this->detail->game_play; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blWPE">
					<?php echo JText::_( 'Bet Limit - Win/Place/EachWay' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="blWPE" id="blWPE" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_wple; ?>" />%
			</td>
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="prizeFormula">
					<?php echo JText::_( 'Prize/Payout Formula' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="prizeFormula" id="prizeFormula" size="10" maxlength="10" value="<?php echo $this->detail->prizeFormula; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blT">
					<?php echo JText::_( 'Bet Limit - Trifecta' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="blT" id="blT" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_t; ?>" />%
			</td>
		</tr>
		<tr>
			<td width="100" align="right" class="key">
				<label for="jackP">
					<?php echo JText::_( 'Jackpot Parent' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="jackP" id="jackP" size="10" maxlength="10" value="<?php echo $this->detail->parentID; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blQ">
					<?php echo JText::_( 'Bet Limit - Quinella' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="blQ" id="blQ" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_q; ?>" />%
			</td>
			
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="startbucks">
					<?php echo JText::_( 'Starting Bucks' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="startbucks" id="startbucks" size="10" maxlength="15" value="<?php echo $this->detail->starting_bbucks; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blF">
					<?php echo JText::_( 'Bet Limit - Flexi' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="blF" id="blF" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_f; ?>" />%
			</td>
		</tr>
		
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="minPrizePool">
					<?php echo JText::_( 'Minimum Prize Pool' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="minPrizePool" id="minPrizePool" size="10" maxlength="10" value="<?php echo '$'.$this->detail->min_prize_pool; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="blE">
					<?php echo JText::_( 'Bet Limit - Exacta' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="blE" id="blE" size="10" maxlength="10" value="<?php echo $this->detail->betlimit_e; ?>" />%
			</td>
		</tr>
	
		<tr>
			<td width="100" align="right" class="key">
				<label for="tournInfo">
					<?php echo JText::_( 'Tournament Status' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="tournInfo" id="tournInfo" size="64" maxlength="250" value="<?php echo $tournPaid; ?>" />
			</td>
			<td width="100" align="right" class="key">
				<label for="tournInfo">
					<?php echo JText::_( 'Tournament Information' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="tournInfo" id="tournInfo" size="100" maxlength="250" value="<?php echo $this->detail->tournInfo; ?>" />
			</td>
		</tr>
	
				 
	</table>
</fieldset>

<form action="<?php echo JRoute::_($this->request_url) ?>" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Tournament Statistics' ); ?></legend>
	<table class="admintable">
		<tr>
			<td width="100" align="right" class="key">
				<label for="centrants">
					<?php echo JText::_( 'Current Entrants' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="centrants" id="centrants" size="32" maxlength="250" value="<?php echo $this->detail->current_entrants; ?>" />
			</td>
			
			<td width="100" align="right" class="key">
				<label for="tbets">
					<?php echo JText::_( 'Total Bets' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="tbets" id="tbets" size="32" maxlength="250" value="<?php echo $this->totalBets ;?>" />
			</td>
			
			
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="ubetters">
					<?php echo JText::_( 'Unique Betters' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="ubetters" id="ubetters" size="32" maxlength="250" value="<?php echo count($this->totalBetters); ?>" />
			</td>
			
			<td width="100" align="right" class="key">
				<label for="tamount">
					<?php echo JText::_( 'Total Bet Amount' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="tamount" id="tamount" size="32" maxlength="250" value="<?php echo '$'.$this->allBets; ?>" />
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="bfees">
					<?php echo JText::_( 'Buy-In Fees Taken' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="bfees" id="bfees" size="32" maxlength="250" value="<?php echo '$'.$this->currentPrizePool[1] ; ?>" />
			</td>
			
			<td width="100" align="right" class="key">
				<label for="ppool">
					<?php echo JText::_( 'Current Prize Pool' ); ?>:
				</label>
			</td>
			<td>
				<input disabled class="text_area" type="text" name="ppool" id="ppool" size="32" maxlength="250" value="<?php echo '$'.$this->currentPrizePool[0] ;?>" />
			</td>
		</tr>
		
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="date">
					<?php echo JText::_( 'Total Bets on each Race + Bets Total' ); ?>:
				</label>
			</td>
			<td>
				<?php
				
				foreach ($this->raceBets as $result) :
					
					$raceNumber = substr($result->tab_race_id, -2);
					$raceNumber = str_replace ("0", "", $raceNumber);
					$racebet = number_format($result->betTotal, 2, '.', '');
			?>
	      	<tr>
	        	<td><?php echo $raceNumber ?></td>
				<td><?php echo $result->betCount ; ?></td>
				<td>$<?php echo $racebet; ?></td>
	        </tr>
			<?php
				endforeach;
			?>
			
			
			</td>
		</tr>
			
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="date">
					<?php echo JText::_( 'Current Tournament Positions' ); ?>:
				</label>
			</td>
			<td>
				<?php
				
				$i=0;
				foreach ($this->tournWinners as $player) :
					$rownum = ($i % 2) ? "2" : "1";
					$totalBetAmount = number_format($player->pBucks, 2, '.', '');

			?>
	      	
	        	<td><?php echo $player->userPIN; ?></td>
				<td><?php echo $player->nickName; ?></td>
				<td><?php echo $player->userEmail; ?></td>
				<td>$<?php echo $totalBetAmount; ?></td>
	       
			<?php
				$i++;
				endforeach;
			?>
			</td>
		</tr>
		
		<tr>
			<td width="100" align="right" class="key">
				<label for="date">
					<?php echo JText::_( 'Top 10 Bet on Runners' ); ?>:
				</label>
			</td>
			<td>
			<?php
				
				$i=0;
				foreach ($this->top10Runners as $runner) :
					$rownum = ($i % 2) ? "2" : "1";
					$totalBetAmount = number_format($runner->betAllTotal, 2, '.', '');

			?>
	      	<tr class="sectiontableentry<?php echo $rownum; ?>">
	        	<td><?php echo trim($runner->selections, ","); ?></td>
				<td>$<?php echo $totalBetAmount; ?></td>
	        </tr>
			<?php
				$i++;
				endforeach;
			?>
			</td>
		</tr>
	</table>
	</fieldset>

<fieldset class="adminform">
	<legend><?php echo JText::_( 'Race Statistics and Control' ); ?></legend>
	<table class="admintable">
<?php 

for ($i=0, $n=count( $this->races ); $i < $n; $i++){
	$row = &$this->races[$i];
		;
		echo '<legend>'; echo JText::_( 'Race '. $row->number.': '. $row->name ); echo '</legend>
		
		<table>
		
		
			<tr>
				<td width="100" align="right" class="key"><label for="tab_meeting_id"> ';
				echo JText::_( 'Time' );
				echo '</label></td>
				<td>';	echo $row->time;	echo '</td>
			</tr>
		
			<tr>
				<td width="100" align="right" class="key"><label for="tab_meeting_id"> ';
				echo JText::_( 'Distance:' );
				echo '</label></td>
				<td>';	echo $row->distance;	echo '</td>
			</tr>
			
			<tr>
				<td width="100" align="right" class="key"><label for="tab_meeting_id"> ';
				echo JText::_( 'Status (Betting):' );
				echo '</label></td>
				<td>';	echo $row->status;	echo '</td>
			</tr>
			
			<tr>
				<td width="100" align="right" class="key"><label for="tab_meeting_id"> ';
				echo JText::_( 'Open for Betting:' );
				echo '</label></td>
				<td>';if ($nowTime < $row->start_unixtimestamp) {echo YES;}else{echo NO;};echo '</td>
			</tr>
					
			<tr>
				<td width="100" align="right" class="key"><label for="tab_meeting_id"> ';
				echo JText::_( 'Clients Paid (race Bets):' );
				echo '</label></td>
				<td>';	if ($row->clients_paid){echo YES;}else{echo NO;};	echo '</td>
			</tr>
			
			<tr>
				<td width="100" align="right" class="key"><label for="tab_meeting_id"> ';
				echo JText::_( 'Stop Bets NOW!:' );
				echo '</label></td>
				<td>'; 
				if ($nowTime < $row->start_unixtimestamp)
				{
					echo '<button type=button class="stop_bets" value="'.$row->id.'" id="'.$row->id.'">Click Here to STOP Taking Bets on this Race</button>';
				}else{
					echo 'BETS CLOSED';
				}	
				echo  '</td></tr></table>';
		
} ?>
	</table>
</fieldset>

	


<div class="clr"></div>

<input type="hidden" name="cid[]" value="<?php echo $this->detail->id; ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="controller" value="tourn_info_detail" />
</form>


