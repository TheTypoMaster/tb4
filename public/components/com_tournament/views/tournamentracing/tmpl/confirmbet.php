<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>
	<div class="mainbdr2">
		<div id="bettixPopWrap">
			<form action="index.php" name="atpBetForm" id="atpBetForm" method="POST">
				<input type="hidden" name="option" value="com_tournament" />
				<input type="hidden" name="controller" value="tournamentracing" />
				<input type="hidden" name="task" value="savebet" />
				<input type="hidden" name="id" value="<?php print $this->tournament->id; ?>" />
				<input type="hidden" name="race_id" value="<?php print $this->race->id; ?>" />
				<input type="hidden" name="bet_type_id" value="<?php print $this->bet_type->id; ?>" />
				<input type="hidden" name="value" value="<?php print $this->value / 100; ?>" />
				<input type="hidden" name="selection" value="<?php print $this->selection; ?>" />

				<div class="bettixPanel">
					<div class="bettixHead">
						<div class="bettixHeadTitle">Tournament Bet Confirmation</div>
						<div class="bettixHeadRace">Race <?php echo $this->race->number; ?> - <?php echo $this->race->name; ?></div>
					</div>

					<div class="bettixTpanel">
						<table width="100%">
							<tr>
								<td class="selHead lft">SELECTION</td>
								<td class="selHead">BET TYPE</td>
								<td class="selHead">AMOUNT</td>
								<td class="selHead">ODDS</td>
							</tr>

<?php
$class = null;
foreach( $this->runner_list as $runner)
{
	$class = (is_null($class) || $class == 2) ? 1 : 2;
	$bet_row = ($this->bet_type->name == 'eachway') ? array('win', 'place') : array($this->bet_type->name);

	foreach($bet_row as $bet_type) {
?>

						<tr>
							<td class="sel lft"><?php echo $this->escape($runner->number . '. ' . $runner->name); ?></td>
							<td class="sel cap w60"><?php echo strtoupper($this->escape($bet_type)); ?></td>
							<td class="sel rgt w60"><?php echo $this->display_value; ?></td>
							<td class="sel w60"><?php if(strtoupper($bet_type)== 'WIN') echo $this->bet_product[$runner->id]['name']['win'];
								elseif(strtoupper($bet_type)== 'PLACE') echo $this->bet_product[$runner->id]['name']['place'];
								else echo 'SuperTab'; ?></td>
						</tr>

<?php

	$class = ($class == 1) ? 2 : 1;
	}
}
?>
					</table>
				</div>

				<div class="bettixFoot">TOTAL BET FOR THIS TICKET: <?php echo $this->display_total; ?></div>
				<div class="bettixFootMsg">Bet cannot be cancelled. To continue to place your bets click SUBMIT BET.</div>
				<div id="button_group">
					<input id="cancelBets" type="button" name="cancelBets" value="CANCEL" onclick="$('sbox-window').close(); "/>
					<input id="confirmBets" type="submit" name="confirmBets" value="SUBMIT BET" onclick="this.disabled=true,this.form.submit();" />
				</div>
				<div id="processingBets" style="display:none"><img src="/templates/topbetta/images/loading-dark.gif" />Processing Bet</div>
				<div class="clear"></div>
			</div>
		</form>
		<div class="clear"></div>
	</div>
</div>
