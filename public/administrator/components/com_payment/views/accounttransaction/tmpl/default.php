<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php?option=com_payment&amp;c=account" method="post" name="adminForm">

<table>
	<tr>
		<td align="left">
		<?php echo JText::_('Filter'); ?>:
			<input type="text" name="filter_account_search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		</td>
		<td>
			<select name="filter_account_transaction_type" onChange="document.adminForm.submit()">
<?php
	foreach( $this->transactionTypes as $transactionType => $transactionLabel )
	{
		$selected = '';
		if($this->lists['transaction_type'] == $transactionType)
		{
			$selected = 'selected="selected" ';
		}
?>

                <option <?php echo $selected?>value="<?php echo htmlspecialchars($transactionType) ?>"><?php echo htmlspecialchars($transactionLabel) ?></option>
<?php
	}
?>
            </select>
		</td>
		<td>
			<?php echo JText::_('From'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['from_date'];?>" class="DatePicker" name="filter_account_from_date" id="from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('To'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['to_date'];?>" class="DatePicker" name="filter_account_to_date" id="to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('From'); ?>:
		</td>
		<td>
			$<input type="text" value="<?php echo $this->lists['from_amount'];?>" name="filter_account_from_amount" id="from_amount" size="5" onkeypress="return isNumberKey(event)" />
		</td>
		<td>
			<?php echo JText::_('To'); ?>:
		</td>
		<td>
			$<input type="text" value="<?php echo $this->lists['to_amount'];?>" name="filter_account_to_amount" id="to_amount" size="5" onkeypress="return isNumberKey(event)" />
		</td>
		
		<td>
			<button onclick="this.form.submit();">
				<?php echo JText::_('Search'); ?>
			</button>
			<button onclick="document.adminForm.filter_account_search.value='';
				document.adminForm.filter_account_transaction_type.value='';
				document.adminForm.filter_account_from_date.value='';
				document.adminForm.filter_account_to_date.value='';
				document.adminForm.filter_account_from_amount.value='';
				document.adminForm.filter_account_to_amount.value='';
				this.form.submit();">
				<?php echo JText::_('Reset'); ?>
			</button>
		</td>
<?php
if( count($this->transactions) > 0 )
{
?>
		<td>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="index.php?option=com_payment&amp;c=account&amp;task=csv_export"><img src="/administrator/components/com_payment/images/export_icon.png" alt="Export Transactions" width="16" height="16" /> export</a>
		</td>
<?php
}
?>
	</tr>
</table>
<table class="adminlist">
	<thead>
	<tr>
		<th width="5%" nowrap="nowrap">
			<?php echo JHTML::_('grid.sort', JText::_( 'ID' ), 'id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHTML::_('grid.sort', JText::_('Recipient Name'), 'recipient', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Recipient Username'), 'recipient_username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Recipient ID'), 'recipient_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHTML::_('grid.sort', JText::_('Giver Name'), 'giver', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Giver Username'), 'giver_username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Giver ID'), 'giver_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHTML::_('grid.sort', JText::_('Transaction Type'), 'type', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="15%" align="center">
			<?php echo JHTML::_('grid.sort', JText::_('Transaction Date'), 'requested_date', $this->lists['order_Dir'], $this->lists['order']); ?>
		</th>
		<th width="5%" align="center">
			<?php echo JHTML::_('grid.sort', JText::_('Amount'), 'amount', $this->lists['order_Dir'], $this->lists['order']); ?>
		</th>
		<th>
			<?php echo JText::_('Notes'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
<?php
	$k = 0;
	$i = 0;
	foreach( $this->transactions as $row )
	{
		$checked = JHTML::_('grid.id', $i, $row->id );
		$link = JRoute::_('index.php?option='. JRequest::getVar( 'option' ) . '&c=account&task=edit&request=' . $row->id );
		
?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo htmlspecialchars($row->id); ?></td>
			<td><?php echo htmlspecialchars($row->recipient); ?></td>
			<td><?php echo htmlspecialchars($row->recipient_username); ?></td>
			<td><?php echo htmlspecialchars($row->recipient_id); ?></td>
			<td><?php echo htmlspecialchars($row->giver); ?></td>
			<td><?php echo htmlspecialchars($row->giver_username); ?></td>
			<td><?php echo htmlspecialchars($row->giver_id); ?></td>
			<td><?php echo htmlspecialchars($row->type); ?></td>
			<td><?php echo htmlspecialchars($row->created_date); ?></td>
			<td>
<?php
		if( $row->amount < 0)
		{
			echo '<div class="negative">'. sprintf('-$%.2f' , abs($row->amount/100)) . '</div>';
		}
		else
		{
			echo '<div class="positive">'. sprintf('$%.2f' , $row->amount/100) . '</div>';
		}
?>
		</td>
			<td><?php echo (nl2br(htmlspecialchars($row->notes)))?></td>
		</tr>
<?php
	}
?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="11">
			<?php echo $this->page->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
</table>

<input type="hidden" name="option"
value="<?php echo JRequest::getVar( 'option' ); ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>