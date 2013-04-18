<?php
	defined('_JEXEC') or die('Restricted access');
	JToolBarHelper::title( JText::_( 'Tournaments' ), 'generic.png' );
	JToolBarHelper::addNew('edit');
	JToolBarHelper::preferences('com_tournament', '350');
?>

<form name="adminForm" action="<?php print $this->form_action; ?>" method="post">
	<table>
		<tr>
			<td align="left">
				<?php echo JText::_('Keyword'); ?>:
				<input name="keyword" type="text" value="<?php echo $this->escape($this->keyword) ?>"></input>
			</td>
			<td align="left">
				<?php echo JText::_('Type'); ?>:
				<select name="private_flag" id="private_flag" onChange="document.adminForm.submit()">
					<?php foreach($this->tournament_type_list as $private_flag => $label) : ?>
					<?php $selected = ''; ?>
					<?php if ($this->private_flag == $private_flag) : ?>
					<?php $selected = 'selected="selected" '; ?>
					<?php endif; ?>
	                <option <?php echo $selected;?>value="<?php echo $this->escape($private_flag) ?>"><?php echo $this->escape($label) ?></option>
					<?php endforeach; ?>
            	</select>
			</td>
			<td>
				<?php echo JText::_('From'); ?>:
			</td>
			<td>
				<input type="text" value="<?php echo $this->escape($this->start_date);?>" class="DatePicker" name="start_date" id="start_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
			</td>
			<td>
				<?php echo JText::_('To'); ?>:
			</td>
			<td>
				<input type="text" value="<?php echo $this->escape($this->end_date);?>" class="DatePicker" name="end_date" id="end_date" alt="{format:'yyyy-mm-dd',yearStart:2010}" />
			</td>
			<td>
				<input type="submit" name="btnFilter" value="&nbsp;&nbsp;Filter&nbsp;&nbsp;"/>
				<input type="button" name="reset" value="&nbsp;&nbsp;Reset&nbsp;&nbsp;" onclick="document.adminForm.keyword.value='';
					document.adminForm.private_flag[1].selected='';
					document.adminForm.start_date.value='';
					document.adminForm.end_date.value='';
					this.form.submit();" />
			</td>
		</tr>
	</table>


	<div id="editcell">
		<table class="adminlist" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th><?php print JHTML::_('grid.sort', JText::_( 'NUM' ), 'id', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Tournament Name'), 't.name', $this->direction, $this->order); ?></th>
                    <th><?php print JHTML::_('grid.sort', JText::_('TOD'), 't.tod_flag', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Parent Name'), 'pt.name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Event Group'), 'eg.name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Type/Sport'), 's.name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Start Time'), 't.start_date', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('End Time'), 't.end_date', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Prize'), 't.jackpot_flag', $this->direction, $this->order); ?></th>
                    <th><?php print JHTML::_('grid.sort', JText::_('FCP'), 't.free_credit_flag', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Game'), 't.jackpot_flag', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Buy-in'), 't.buy_in', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Entry-fee'), 't.entry_fee', $this->direction, $this->order); ?></th>
					<th><?php print JText::_('Ent.'); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Status'), 't.status_flag', $this->direction, $this->order); ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php $i=1; ?>
			<?php foreach($this->tournament_list as $tournament): ?>
				<tr class="<?php echo $i % 2 == 0 ? 'row1' : 'row0'?>">
					<td><?php print JText::_($tournament->id); ?></td>
					<td>
						<a href="<?php print $tournament->edit_link; ?>"><?php print JText::_($tournament->name); ?></a>
					</td>
                    <td><?php print JText::_((isset($this->tod_flag_list[$tournament->tod_flag])) ? $this->tod_flag_list[$tournament->tod_flag] : '' ) ?></td>
					<td><?php print JText::_($tournament->parent_name); ?></td>
					<td><?php print JText::_($tournament->event_group_name); ?></td>
					<td><?php print JText::_($tournament->sport_name); ?></td>
					<td><?php print JText::_($tournament->start_date); ?></td>
					<td><?php print JText::_($tournament->end_date); ?></td>
					<td><?php print JText::_($tournament->prize_formula) . ' (' . JText::_($tournament->prize_pool) . ')'; ?></td>
                    <td><?php print JText::_(($tournament->free_credit_flag == 1) ? 'Yes' : '' ) ?></td>
					<td><?php print JText::_($tournament->gameplay); ?></td>
					<td><?php print JText::_($tournament->buy_in); ?></td>
					<td><?php print JText::_($tournament->entry_fee); ?></td>
					<td><?php print JText::_($tournament->entrant_count); ?></td>
					<td><?php print JText::_($tournament->status) . ' ' . JText::_($tournament->cancelled); ?></td>                    
					<td><a href="<?php print $tournament->view_link; ?>">View</a></td>
					<td><a href="<?php print $tournament->clone_link; ?>">Clone</a></td>
					<td><a href="<?php print $tournament->cancel_link; ?>">Cancel</a></td>
					<td><a href="<?php print $tournament->delete_link; ?>" onclick="return confirm('Are you sure you want to delete the tournament <?php print $tournament->name; ?>?');">Delete</a></td>
				</tr>
				<?php $i++; ?>
			<?php endforeach; ?>
			</tbody>
			<tfoot>
				<td colspan="17">
					<?php print $this->pagination; ?>
				</td>
			</tfoot>
		</table>
	</div>
	<input type="hidden" name="option" value="com_tournament" />
	<input type="hidden" name="task" value="listview" />
	<input type="hidden" name="filter_order" value="<?php print $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php print $this->direction; ?>" />
	<input type="hidden" name="limitstart" value="" />
</form>
