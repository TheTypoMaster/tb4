<?php
defined('_JEXEC') or die('Restricted access');
?>

<form name="adminForm" action="index.php" method="post" id="filterFrm">
	
	
	<table class="adminlist" cellspacing="0" style="width:50%">
		<thead>
			<tr>
				<th><?php echo JHTML::_('grid.sort', JText::_('id'), 'id', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Group'), 'group', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Description'), 'description', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Parent Group ID'), 'parent_group_id', $this->direction, $this->order); ?></th>
				
				<th>Edit</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if(!empty($this->tournament_group_list)){
			foreach($this->tournament_group_list as $tournament_group) { ?>
				<tr>

					<td><?php echo JText::_($tournament_group->id); ?>
					</td>
					<td><?php echo JText::_($tournament_group->group); ?>
					</td>
					<td><?php echo JText::_($tournament_group->description); ?>
					</td>
					<td><?php echo JText::_($tournament_group->parent_group_group); ?>
					</td>
					
					<td><?php echo '<a href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentgroups&task=edit&id={$tournament_group->id}") . '">Edit</a>'?></td>
				</tr>
		<?php }
		}
		?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="6">
				<?php echo $this->pagination; ?>
			</td>
			</tr>
		</tfoot>
	</table>
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentgroups' />
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->direction; ?>" />
</form>
