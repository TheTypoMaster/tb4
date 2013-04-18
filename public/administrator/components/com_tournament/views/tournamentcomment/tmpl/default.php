<?php
defined('_JEXEC') or die('Restricted access');
?>

<form name="adminForm" action="index.php?option=com_tournament&controller=tournamentcomment" method="post" id="filterFrm">
	<table>
		<tr>
			<td align="left">
			<?php echo JText::_('Tournament No.'); ?>:
				<input name="tournamentId" type="text" value="<?php echo $this->escape($this->tournament_id) ?>"></input>
			</td>
			<td align="left">
			<?php echo JText::_('Username'); ?>:
				<input name="username" type="text" value="<?php echo $this->escape($this->username) ?>"></input>
			</td>
			<td align="left">
			<?php echo JText::_('Visible Only'); ?>:
				<input name="visible" type="checkbox"<?php echo $this->visible ? ' checked="checked"' : '' ?>"></input>
			</td>
			<td>
				<input type="submit" name="btnFilter" value="&nbsp;&nbsp;Filter&nbsp;&nbsp;"/>
				<input type="button" name="reset" value="&nbsp;&nbsp;Reset&nbsp;&nbsp;" onclick="document.adminForm.tournamentId.value='';
					document.adminForm.username.value='';
					document.adminForm.visible.checked='';
					this.form.submit();" />
			</td>
		</tr>
	</table>
	<table class="adminlist" cellspacing="0">
		<thead>
			<tr>
				<th><?php echo JHTML::_('grid.sort', JText::_('ID'), 'tc.id', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Username'), 'u.username', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Tournament'), 't.id', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Time of Comment'), 'tc.created_date', $this->direction, $this->order); ?></th>
				<th><?php echo JText::_('Visible'); ?></th>
				<th><?php echo JText::_('Comment'); ?></th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($this->comment_display_list)) : ?>
			<?php foreach($this->comment_display_list as $comment) : ?>
				<tr>
					<td><?php echo $this->escape($comment['id']); ?></td>
					<td><?php echo $this->escape($comment['username']); ?></td>
					<td>
						<?php echo '<a href="' . JRoute::_($comment['tournament_link']) . '" target="_blank">' . $comment['tournament'] . '</a>'; ?>
					</td>
					<td><?php echo $this->escape($comment['created_date']); ?></td>
					<td><?php echo $this->escape($comment['visible']); ?></td>
					<td><?php echo $this->escape($comment['comment']); ?></td>
					<td><?php echo '<a href="' . JRoute::_($comment['delete_link']) . '" onClick="return confirm(\'Do you really want to delete this comment?\');">Delete</a>'?></td>
				</tr>
			<?php endforeach; ?>
		<? endif; ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pagination; ?>
			</td>
			</tr>
		</tfoot>
	</table>
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentcomment' />
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->direction; ?>" />
	<input type="hidden" name="limitstart" value="" />
</form>
