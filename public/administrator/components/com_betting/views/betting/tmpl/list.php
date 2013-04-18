<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>
<form action="" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left">
			<?php echo JText::_('Filter'); ?>:
				<input type="text" name="filter_bets_keyword" id="search" value="<?php echo $this->lists['keyword'];?>" class="text_area" onchange="document.adminForm.submit();" />
			</td>
			<td>
				<select name="filter_bets_result_type" onChange="document.adminForm.submit()">
				<?php foreach( $this->bet_result_type_list as $result_type => $bet_result_label) : ?>
				<?php $selected = ''; ?>
				<?php if($this->lists['result_type'] == $result_type) : ?>
				<?php $selected = 'selected="selected" '; ?>
				<?php endif; ?>
	                <option <?php echo $selected?>value="<?php echo $this->escape($result_type) ?>"><?php echo $this->escape($bet_result_label) ?></option>
				<?php endforeach; ?>
	            </select>
			</td>
			<td>
				<?php echo JText::_('From'); ?>:
			</td>
			<td>
				<input type="text" value="<?php echo $this->lists['from_date'];?>" class="DatePicker" name="filter_bets_from_date" id="from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
			</td>
			<td>
				<?php echo JText::_('To'); ?>:
			</td>
			<td>
				<input type="text" value="<?php echo $this->lists['to_date'];?>" class="DatePicker" name="filter_bets_to_date" id="to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
			</td>
			<td>
				<?php echo JText::_('From'); ?>:
			</td>
			<td>
				$<input type="text" value="<?php echo $this->lists['from_amount'];?>" name="filter_bets_from_amount" id="from_amount" size="5" onkeypress="return isNumberKey(event)" />
			</td>
			<td>
				<?php echo JText::_('To'); ?>:
			</td>
			<td>
				$<input type="text" value="<?php echo $this->lists['to_amount'];?>" name="filter_bets_to_amount" id="to_amount" size="5" onkeypress="return isNumberKey(event)" />
			</td>
			
			<td>
				<button onclick="this.form.submit();">
					<?php echo JText::_('Search'); ?>
				</button>
				<button onclick="document.adminForm.filter_bets_keyword.value='';
					document.adminForm.filter_bets_result_type.value='';
					document.adminForm.filter_bets_from_date.value='';
					document.adminForm.filter_bets_to_date.value='';
					document.adminForm.filter_bets_from_amount.value='';
					document.adminForm.filter_bets_to_amount.value='';
					this.form.submit();">
					<?php echo JText::_('Reset'); ?>
				</button>
			</td>
		</tr>
	</table>

	<?php if (empty($this->bet_display_list)) :?>
	<p>There are no bets to list.</p>
	<?php else : ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('Ticket No'); ?>
				</th>
				<th>
					<?php echo JText::_('Tote Ticket No'); ?>
				</th>
				<th>
					<?php echo JText::_('User Name'); ?>
				</th>
				<th>
					<?php echo JText::_('Time of Bet'); ?>
				</th>
				<th>
					<?php echo JText::_('Bet Selections'); ?>
				</th>
				<th>
					<?php echo JText::_('Bet Type'); ?>
				</th>
				<th>
					<?php echo JText::_('Amount Bet'); ?>
				</th>
				<th>
					<?php echo JText::_('Total Bet'); ?>
				</th>
				<th>
					<?php echo JText::_('Dividend'); ?>
				</th>
				<th>
					<?php echo JText::_('Paid'); ?>
				</th>
				<th>
					<?php echo JText::_('Result'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->bet_display_list as $bet_id => $bet) : ?>
			<tr class="<?php echo $bet['row_class']; ?>">
				<td><?php echo $this->escape($bet_id); ?></td>
				<td><?php echo $bet['external_bet_id']; ?></td>
				<td><?php echo $this->escape($bet['username']); ?></td>
				<td><?php echo $this->escape($bet['bet_time']); ?></td>
				<td><?php echo $this->escape($bet['label']); ?></td>
				<td><?php echo $this->escape($bet['bet_type']); ?></td>
				<td><?php echo $this->escape($bet['amount']); ?></td>
				<td><?php echo $this->escape($bet['bet_total']); ?></td>
				<td><?php echo $bet['dividend']; ?></td>
				<td><?php echo $bet['paid']; ?></td>
				<td><?php echo $bet['result']; ?></td>
			</tr>
			<?php if ($refund_row = $bet['half_refund']) :?>
			<tr class="<?php echo $bet['row_class'] ?>">
				<td><?php echo $this->escape($bet_id) ?></td>
				<td><?php echo $this->escape($bet['username']); ?></td>
				<td><?php echo $this->escape($bet['bet_time'])?></td>
				<td><div class="scratched"><?php echo $this->escape($refund_row['label'])?></div></td>
				<td><?php echo $this->escape($refund_row['bet_type']) ?></td>
				<td><?php echo $refund_row['amount'] ?></td>
				<td><?php echo $refund_row['bet_total'] ?></td>
				<td><?php echo $refund_row['dividend'] ?></td>
				<td><?php echo $this->escape($refund_row['paid']) ?></td>
				<td><?php echo $this->escape($refund_row['result']) ?></td>
			</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="11"><?php echo $this->pagination; ?></td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="limitstart" value="" />
	<?php endif; ?>
</form>