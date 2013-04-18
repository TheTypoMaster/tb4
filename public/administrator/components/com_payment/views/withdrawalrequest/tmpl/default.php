<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php?option=com_payment&amp;c=withdrawal" method="post" name="adminForm">

<table>
	<tr>
		<td align="left">
			<?php echo JText::_('Filter'); ?>:
		</td>
		<td>
			<input type="text" name="filter_withdrawal_search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		</td>
		<td>
			<?php echo JText::_('Requested from'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['requested_from_date'];?>" class="DatePicker" name="filter_withdrawal_requested_from_date" id="requested_from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('To'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['requested_to_date'];?>" class="DatePicker" name="filter_withdrawal_requested_to_date" id="requested_to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('Amount From'); ?>:

			$<input type="text" value="<?php echo $this->lists['from_amount'];?>" name="filter_withdrawal_from_amount" id="from_amount" size="5" onkeypress="return isNumberKey(event)" />

			<?php echo JText::_('To'); ?>:

			$<input type="text" value="<?php echo $this->lists['to_amount'];?>" name="filter_withdrawal_to_amount" id="to_amount" size="5" onkeypress="return isNumberKey(event)" />
		</td>
<?php
if( count($this->requests) > 0 )
{
?>
		<td>&nbsp;&nbsp;|&nbsp;&nbsp;</td>
<?php
}
?>
	</tr>
	<tr>
		<td align="left">
			<?php echo JText::_('Status'); ?>:
		</td>
		<td>
			<select name="filter_withdrawal_status" onChange="document.adminForm.submit()">
<?php
	foreach( $this->statusOptions as $statusOption => $statusLabel )
	{
		$selected = '';
		if($this->lists['status'] == $statusOption)
		{
			$selected = 'selected="selected" ';
		}
?>

                <option <?php echo $selected?>value="<?php echo htmlspecialchars($statusOption) ?>"><?php echo htmlspecialchars($statusLabel) ?></option>
<?php
	}
?>
            </select>
		</td>
		<td>
			<?php echo JText::_('Fulfilled from'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['fulfilled_from_date'];?>" class="DatePicker" name="filter_withdrawal_fulfilled_from_date" id="fulfilled_from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('To'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['fulfilled_to_date'];?>" class="DatePicker" name="filter_withdrawal_fulfilled_to_date" id="fulfilled_to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td align="right">
			<button onclick="this.form.submit();">
				<?php echo JText::_('Search'); ?>
			</button>
			<button onclick="document.adminForm.filter_withdrawal_search.value='';
				document.adminForm.filter_withdrawal_requested_from_date.value='';
				document.adminForm.filter_withdrawal_requested_to_date.value='';
				document.adminForm.filter_withdrawal_fulfilled_from_date.value='';
				document.adminForm.filter_withdrawal_fulfilled_to_date.value='';
				document.adminForm.filter_withdrawal_from_amount.value='';
				document.adminForm.filter_withdrawal_to_amount.value='';
				document.adminForm.filter_withdrawal_status.value='';
				this.form.submit();">
				<?php echo JText::_('Reset'); ?>
			</button>
		</td>
<?php
if( count($this->requests) > 0 )
{
?>
		<td>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="index.php?option=com_payment&amp;c=withdrawal&amp;task=csv_export"><img src="/administrator/components/com_payment/images/export_icon.png" alt="Export Transactions" width="16" height="16" /> export</a>
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
			<?php echo JHTML::_('grid.sort', JText::_('Requester Name'), 'requester', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHTML::_('grid.sort', JText::_('Requester Username'), 'username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Requester ID'), 'requester_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHTML::_('grid.sort', JText::_('Amount'), 'amount', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="15%">
			<?php echo JHTML::_('grid.sort', JText::_('Withdrawal Type'), 'withdrawal_type', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="15%" align="center">
			<?php echo JHTML::_('grid.sort', JText::_('Rquested Date'), 'requested_date', $this->lists['order_Dir'], $this->lists['order']); ?>
		</th>
		<th width="15%">
			<?php echo JHTML::_('grid.sort', JText::_('Fulfilled Date'), 'fulfilled_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_('Approved'), 'approved_flag', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JText::_('Notes'); ?>
		</th>
		<th width="5%">
			<?php echo JText::_('View'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
<?php
	$k = 0;
	$i = 0;
	foreach( $this->requests as $row )
	{
		$checked = JHTML::_('grid.id', $i, $row->id );
		$link = JRoute::_('index.php?option='. JRequest::getVar( 'option' ) . '&c=withdrawal&task=edit&request=' . $row->id );
		
?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo htmlspecialchars($row->id); ?></td>
			<td><?php echo htmlspecialchars($row->requester); ?></td>
			<td><?php echo htmlspecialchars($row->username); ?></td>
			<td><?php echo htmlspecialchars($row->requester_id); ?></td>
			<td><?php echo htmlspecialchars(sprintf('$%.2f' , $row->amount/100)); ?></td>
			<td>
				<?php echo htmlspecialchars($row->withdrawal_type); ?>
				<?php if('paypal' == $row->withdrawal_type && $row->paypal_id)
						{
						echo ( ' - ' . htmlspecialchars($row->paypal_id) );
						}elseif('moneybookers' == $row->withdrawal_type && $row->moneybookers_id)
						{
						echo ( ' - ' . htmlspecialchars($row->moneybookers_id) );
						}
						?>
			</td>
			<td><?php echo htmlspecialchars($row->requested_date); ?></td>
			<td><?php echo (htmlspecialchars($row->fulfilled_date))?></td>
			<td><?php echo ($row->approved_flag === null ? 'Pending' : ($row->approved_flag ? 'Yes' : 'No'))?></td>
			<td><?php echo (htmlspecialchars($row->notes))?></td>
			<td class="view_icon"><a href="<?php echo $link; ?>"><img src="<?php echo JURI::base() ?>components/com_payment/images/magnifier_icon.png" width="11" height="11" alt="view" /></a></td>
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