<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<form action="index.php?option=com_topbetta_user" method="post" name="adminForm">

<table>
	<tr>
		<td align="left">
		<?php echo JText::_('Filter'); ?>:
		</td>
		<td>
			<input type="text" name="filter_topbettauser_search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
		</td>
		<td>
			<?php echo JText::_('Registered from'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['registration_from_date'];?>" class="DatePicker" name="filter_topbettauser_registration_from_date" id="registration_from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('To'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['registration_to_date'];?>" class="DatePicker" name="filter_topbettauser_registration_to_date" id="registration_to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('State'); ?>:
		</td>
		<td>
			<select name="filter_topbettauser_state" onChange="document.adminForm.submit()">
				<option value="">All States</option>
<?php
	foreach( $this->options['state'] as $k => $v )
	{
		$selected = '';
		if($this->lists['state'] == $k)
		{
			$selected = 'selected="selected" ';
		}
?>

                <option <?php echo $selected?>value="<?php echo $this->escape($k) ?>"><?php echo $this->escape($v) ?></option>
<?php
	}
?>
            </select>
		</td>
		<td>
			<?php echo JText::_('Marketing Opt-in'); ?>:
		</td>
		<td>
			<select name="filter_topbettauser_marketing" onChange="document.adminForm.submit()">
				<option value="">All</option>
				<option value="yes"<?php echo $this->lists['marketing'] == 'yes' ? ' selected="selected"' : '' ?>>Yes</option>
				<option value="no"<?php echo $this->lists['marketing'] == 'no' ? ' selected="selected"' : '' ?>>No</option>
            </select>
		</td>
<?php
if( count($this->users) > 0 )
{
?>
		<td>
			&nbsp;&nbsp;|&nbsp;&nbsp;
		</td>
<?php
}
?>
	</tr>
	<tr>
		<td>
			<?php echo JText::_('Heard About'); ?>:
		</td>
		<td>
			<select name="filter_topbettauser_heard_about" onChange="document.adminForm.submit()">
				<option value="">All</option>
<?php
	foreach( $this->options['heard_about'] as $k => $v )
	{
		$selected = '';
		if($this->lists['heard_about'] == $k)
		{
			$selected = 'selected="selected" ';
		}
?>

                <option <?php echo $selected?>value="<?php echo $this->escape($k) ?>"><?php echo $this->escape($v) ?></option>
<?php
	}
?>
            </select>
		</td>
		<td>
			<?php echo JText::_('Last Login From'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['login_from_date'];?>" class="DatePicker" name="filter_topbettauser_login_from_date" id="login_from_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('To'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->lists['login_to_date'];?>" class="DatePicker" name="filter_topbettauser_login_to_date" id="login_to_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
		</td>
		<td>
			<?php echo JText::_('Status'); ?>:
		</td>
		<td>
			<select name="filter_topbettauser_status" onChange="document.adminForm.submit()">
				<option value="">All</option>
				<option value="active"<?php echo $this->lists['status'] == 'active' ? ' selected="selected"' : '' ?>>Active</option>
				<option value="inactive"<?php echo $this->lists['status'] == 'inactive' ? ' selected="selected"' : '' ?>>Inactive</option>
            </select>
		</td>
		<td colspan="2" align="right">
			<button onclick="this.form.submit();">
				<?php echo JText::_('Search'); ?>
			</button>
			<button onclick="document.adminForm.filter_topbettauser_search.value='';
				document.adminForm.filter_topbettauser_state.value='';
				document.adminForm.filter_topbettauser_heard_about.value='';
				document.adminForm.filter_topbettauser_status.value='';
				document.adminForm.filter_topbettauser_marketing.value='';
				document.adminForm.filter_topbettauser_registration_from_date.value='';
				document.adminForm.filter_topbettauser_registration_to_date.value='';
				document.adminForm.filter_topbettauser_login_from_date.value='';
				document.adminForm.filter_topbettauser_login_to_date.value='';
				this.form.submit();">
				<?php echo JText::_('Reset'); ?>
			</button>
		</td>
<?php
if( count($this->users) > 0 )
{
?>
		<td>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<a href="index.php?option=com_topbetta_user&amp;task=csv_export"><img src="/administrator/components/com_topbetta_user/images/export_icon.png" alt="Export Users" width="16" height="16" /> export</a>
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
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('User ID'), 'user_id', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_('Username'), 'username', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_('First Name'), 'first_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_('Last Name'), 'last_name', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_('Account Balance'), 'account_balance', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="5%">
			<?php echo JHTML::_('grid.sort', JText::_('Tournament Dollars'), 'tournament_balance', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th width="10%">
			<?php echo JHTML::_('grid.sort', JText::_('Email'), 'email', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Registration Date '), 'registerDate', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Last Login'), 'lastvisitDate', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JHTML::_('grid.sort', JText::_('Self Exclusion Date'), 'self_exclusion_date', $this->lists['order_Dir'], $this->lists['order'] ); ?>
		</th>
		<th>
			<?php echo JText::_('DOB') ?>
		</th>
		<th>
			<?php echo JText::_('Mobile') ?>
		</th>
		<th>
			<?php echo JText::_('Home Phone') ?>
		</th>
		<th>
			<?php echo JText::_('Heard About') ?>
		</th>
		<th>
			<?php echo JText::_('Marketing Opt-in') ?>
		</th>
		<th>
			<?php echo JText::_('Status') ?>
		</th>
        <th>
			Promo code
		</th>
		<th>
			<?php echo JText::_('Source') ?>
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
	foreach( $this->users as $row )
	{
		$link = JRoute::_('index.php?option='. JRequest::getVar( 'option' ) . '&task=edit&user_id=' . $row->user_id );
?>
		<tr class="<?php echo "row$k"; ?>">
			<td><?php echo $this->escape($row->id); ?></td>
			<td><?php echo $this->escape($row->user_id); ?></td>
			<td><?php echo $this->escape($row->username); ?></td>
			<td><?php echo $this->escape($row->first_name); ?></td>
			<td><?php echo $this->escape($row->last_name); ?></td>
			<td align="right"><?php echo $this->escape('$' . number_format($row->account_balance / 100, 2, '.', ',')); ?></td>
			<td align="right"><?php echo $this->escape('$' . number_format($row->tournament_balance / 100, 2, '.', ',')); ?></td>
			<td><?php echo $this->escape($row->email); ?></td>
			<td><?php echo JHTML::_('date', $row->registerDate, '%Y-%m-%d %H:%M:%S'); ?></td>
			<td><?php echo JHTML::_('date', $row->lastvisitDate, '%Y-%m-%d %H:%M:%S'); ?></td>
			<td><?php echo $this->escape($row->self_exclusion_date); ?></td>
			<td><?php echo $this->escape($row->dob_day . '/' . $row->dob_month . '/' . $row->dob_year); ?></td>
			<td><?php echo $this->escape($row->msisdn); ?></td>
			<td><?php echo $this->escape($row->phone_number); ?></td>
			<td><?php echo $this->escape($row->heard_about ? $this->options['heard_about'][$row->heard_about] : ''); ?></td>
			<td><?php echo $this->escape($row->marketing_opt_in_flag ? 'Yes' : 'No'); ?></td>
			<td><?php echo $this->escape($row->block ? 'Inactive' : 'Active'); ?></td>
            <td><?php echo $this->escape($row->promo_code); ?></td>
			<td><?php echo (strlen($this->escape($row->source)) > 20) ? substr($this->escape($row->source),0,20)."<a href='#' title='$this->escape($row->source)'>...</a>" : $this->escape($row->source); ?></td>
			<td class="view_icon"><a href="<?php echo $link; ?>"><img src="<?php echo JURI::base() ?>components/com_payment/images/magnifier_icon.png" width="11" height="11" alt="view" /></a></td>
		</tr>
<?php
	}
?>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="19">
			<?php echo $this->page->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
</table>

<div>
	Total Account Balance of All Users: <strong><?php echo $this->escape('$' . number_format($this->total_account_balance/100, 2, '.', ',')) ?></strong><br />
	Total Tournament Dollars of All Users: <strong><?php echo $this->escape('$' . number_format($this->total_tournament_balance/100, 2, '.', ',')) ?></strong>
</div>

<input type="hidden" name="option"
value="<?php echo JRequest::getVar( 'option' ); ?>" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="hidemainmenu" value="0" />
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
