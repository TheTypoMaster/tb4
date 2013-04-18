<?php // no direct access
defined('_JEXEC') or die('Restricted access');

?>
	<div class="mainbdr2">
		<div id="bettixPopWrap">
			<form action="index.php" name="atpBetForm" id="atpBetForm" method="POST">
				<input type="hidden" name="option" value="com_betting" />
				<input type="hidden" name="task" value="savebet" />
				<input type="hidden" name="id" value="<?php print $this->meeting->id; ?>" />
				<input type="hidden" name="event_id" value="<?php print $this->event_id; ?>" />
				<input type="hidden" name="race_id" value="<?php print $this->race->id; ?>" />
				<input type="hidden" name="bet_type_id" value="<?php print $this->bet_type->id; ?>" />
				<input type="hidden" name="value" value="<?php print $this->value / 100; ?>" />
				<input type="hidden" name="flexi" value="<?php print $this->flexi_flag; ?>" />
				<input type="hidden" name="bet_origin" value="<?php print $this->bet_origin; ?>" />
				

				<?php foreach ($this->selection_list as $pos => $selections) : ?>
				<?php 	foreach ($selections as $selection) :?>
				<input type="hidden" name="selection[<?php echo $this->escape($pos); ?>][]" value="<?php echo $this->escape($selection); ?>" />
				<input type="hidden" name="wager_id[<?php echo $this->escape($pos); ?>][<?php echo $this->escape($this->runner_list_by_id[$selection]->number); ?>][]" value="<?php echo $this->escape($this->runner_list_by_id[$selection]->wager_id); ?>" />
				<input type="hidden" name="bet_product[<?php echo $this->escape($pos); ?>][<?php echo $this->escape($this->runner_list_by_id[$selection]->number); ?>]" value="<?php print $this->bet_product[$selection]['id']; ?>" />
				<?php 	endforeach; ?>
				<?php endforeach; ?>
				<div class="bettixPanel">
					<div class="bettixHead-blue">
						<div class="bettixHead-icon"><img src="templates/topbetta/images/icon-livebet.png" border="0" alt="Live Betting"/></div>
						<div class="bettixHeadTitle">Bet Confirmation</div>
						<div class="bettixHeadRace">Race <?php echo $this->race->number ?> - <?php echo $this->escape($this->race->name) ?></div>
					</div>

					<div class="bettixTpanel">
						<table width="100%">
							<tr>
								<td class="selHead lft">SELECTION</td>
								<td class="selHead">BET TYPE</td>
								<td class="selHead">FLEXI %</td>
								<td class="selHead">AMOUNT</td>
								<td class="selHead">TOTAL</td>
								<td class="selHead">ODDS</td>
							</tr>

							<?php $class = null; ?>
							<?php foreach( $this->display_bet_list as $bet) : ?>
							<?php $class = (is_null($class) || $class == 2) ? 1 : 2; ?>

							<tr>
								<td class="sel lft"><?php echo $this->escape($bet['selection']); ?></td>
								<td class="sel cap w60"><?php echo $this->escape($bet['bet_type']); ?></td>
								<td class="sel w60"><?php echo $this->escape($bet['flexi']); ?></td>
								<td class="sel rgt w60"><?php echo $this->escape($bet['bet_amount']); ?></td>
								<td class="sel rgt w60"><?php echo $this->escape($bet['total_amount']); ?></td>
								<td class="sel w60"><?php if(strtoupper($bet['bet_type'])== 'WIN') echo $this->bet_product[$bet['runner_number']]['name']['win'];
								elseif(strtoupper($bet['bet_type'])== 'PLACE') echo $this->bet_product[$bet['runner_number']]['name']['place'];
								else echo 'SuperTab'; ?></td>
							</tr>

							<?php $class = ($class == 1) ? 2 : 1; ?>
							<?php endforeach; ?>
					</table>
				</div>

				<div class="bettixFoot">TOTAL BET FOR THIS TICKET: <span class="bettixFootAmount"><?php echo $this->display_total; ?></span></div>
				<div class="bettixFootMsg">Bet cannot be cancelled. To continue to place your bets click SUBMIT BET.</div>
				<div id="button_group">
                <?php if ($this->funds['tournament_dollars'] > 0) { ?>
                	<div class="bettixFootMsg" style="border:2px dotted #CCC; margin:5px 15px 5px 15px; background:#f2f2f2; padding:5px;"><input type="checkbox" name="chkFreeBet"   
                		<?php echo ($this->funds['tournament_dollars'] > 0) ? 'checked="checked"' : '' ?> <?php echo ($this->display_total > $this->funds['tournament_dollars'] || $this->display_total > $this->funds['account_balance']) ? 'onclick="this.checked=true;"' : '' ?> /> 
                	Use <span class="bettixFootAmount"><?php echo '$ '.(($this->display_total <= $this->funds['tournament_dollars']) ? number_format($this->display_total,2,'.',',') : number_format($this->funds['tournament_dollars'],2,'.',',')); ?></span> from my <span class="bettixFootAmount"><?= '$ '.number_format($this->funds['tournament_dollars'], 2, '.', ',') ?></span> free  credit</div>
                <?php } ?>
					<input id="cancelBets" type="button" name="cancelBets" value="CANCEL" onclick="$('sbox-window').close(); "/>
					<input id="confirmBets" type="submit" name="confirmBets" value="SUBMIT BET" />
				</div>
				<div class="clear"></div>
			</div>
		</form>
		<div class="clear"></div>
	</div>
</div>
