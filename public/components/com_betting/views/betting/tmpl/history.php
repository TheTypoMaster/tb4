<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<div id="bettaWrap" class="min-height-360">
	<div class="moduletable">
		<h3>My Account</h3>
		<div class="hdrBar">
			<div id="hdrBar_trans"></div>
			<span class="transaction_title">Betting History</span>
			<div id="date_select"><span class="date_select_txt">DATE: </span><?php echo $this->current_date; ?></div>
		</div>
		<form action="<?php echo JURI::current(); ?>" method="post" name="filter_history_form">
			<table width="100%">
				<tr>
				<?php foreach ($this->nav_list as $nav) :?>
					<td>
					<?php if ($nav == $this->current_nav) :?>
						<?php echo ucwords($nav); ?> Bets
					<?php else : ?>
						<a href="/user/account/betting-history/<?php echo $nav == 'all' ? '' : 'type/' . $nav; ?>"><?php echo ucwords($nav) ?> Bets
					<?php endif; ?>
					</td>
				<?php endforeach ?>
					<td>
						Dates from:
					</td>
					<td>
						<input type="text" value="<?php echo $this->lists['from_date'];?>" class="DatePicker" name="filter_history_from_date" id="from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
					</td>
					<td>
						to:
					</td>
					<td>
						<input type="text" value="<?php echo $this->lists['to_date'];?>" class="DatePicker" name="filter_history_to_date" id="to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
					</td>
					<td>
						<button onclick="this.form.submit();">
							<?php echo JText::_('Search'); ?>
						</button>
						<button onclick="
							document.filter_history_form.filter_history_from_date.value='';
							document.filter_history_form.filter_history_to_date.value='';
							this.form.submit();">
							<?php echo JText::_('Reset'); ?>
						</button>
					</td>
				</tr>
			</table>
		</form>
	</div>

	<?php if($this->bet_display_list) :?>
	<table id="receipt" border="1" class="mytrans" width="100%">
		<tr>
			<th width="7%">TICKET No.</th>
			<th width="13%">TIME OF BET</th>
			<th width="24%">BET SELECTIONS</th>
			<th width="7%">BET TYPE</th>
			<th width="11%">AMOUNT BET (AUD)</th>
			<th width="10%"> BET (AUD)</th>
			<th width="8%">FREE CREDIT</th>
			<th width="7%">DIVIDEND</th>
			<th width="6%">PAID (AUD)</th>
			<th width="7%">STATUS</th>
		</tr>
		
		<?php foreach($this->bet_display_list as $bet_id => $bet) :?>
		<tr class="<?php echo $bet['row_class'] ?>">
			<td><?php echo $this->escape($bet_id) ?></td>
			<td><?php echo $this->escape($bet['bet_time'])?></td>
			<td><a href="<?php echo $this->escape($bet['link']) ?>"><?php echo $this->escape($bet['label'])?></a></td>
			<td><?php echo $this->escape($bet['bet_type'])?></td>
			<td><?php echo $this->escape($bet['amount'])?></td>
			<td><?php echo $this->escape($bet['bet_total'])?></td>
			<td><?php echo $this->escape($bet['bet_freebet_amount'])?></td>
			<td><?php echo $bet['dividend'] ?></td>
			<td><?php echo $bet['paid'] ?></td>
			<td><?php echo $bet['result'] ?></td>
		</tr>
		<?php if ($refund_row = $bet['half_refund']) :?>
		<tr class="<?php echo $bet['row_class'] ?>">
			<td><?php echo $this->escape($bet_id) ?></td>
			<td><?php echo $this->escape($bet['bet_time'])?></td>
			<td><div class="scratched"><?php echo $this->escape($refund_row['label'])?></div></td>
			<td><?php echo $this->escape($refund_row['bet_type']) ?></td>
			<td><?php echo $refund_row['amount'] ?></td>
			<td><?php echo $refund_row['bet_total'] ?></td>
			<td><?php echo $this->escape($bet['bet_freebet_amount'])?></td>
			<td><?php echo $refund_row['dividend'] ?></td>
			<td><?php echo $this->escape($refund_row['paid']) ?></td>
			<td><?php echo $this->escape($refund_row['result']) ?></td>
		</tr>
		<?php endif ?>
		<?php endforeach ?>
	</table>

	<?php if ($this->current_nav == 'all') :?>
		<?php echo preg_replace('/\/index.php\?option=com_betting&amp;task=bettinghistory(&amp;limitstart=){0,1}/s', '/user/account/betting-history/', $this->pagination); ?>
	<?php else : ?>
		<?php echo preg_replace('/\/index.php\?option=com_betting&amp;task=bettinghistory&amp;result_type=' . $this->escape($this->current_nav) . '(&amp;limitstart=){0,1}/s', '/user/account/betting-history/type/'. $this->escape($this->current_nav) . '/', $this->pagination); ?>
	<?php endif; ?>
	<br /><br />
	<?php else : ?>
	<p>There are no bets to list.</p>
	<?php endif ?>
</div>
