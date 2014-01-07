<?php
defined('_JEXEC') or die('Restricted access');
?>



<form name="adminForm" action="index.php" method="post" id="filterFrm">

	<table class="adminlist" cellspacing="0" style="width:50%">
		<thead>
			<tr>
				<th><?php echo JHTML::_('grid.sort', JText::_('id'), 'id', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Name'), 'label', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Description'), 'description', $this->direction, $this->order); ?></th>
				<th><?php echo JHTML::_('grid.sort', JText::_('Parent Label'), 'parent_label_id', $this->direction, $this->order); ?></th>
				
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if(!empty($this->tournament_label_list)){
			foreach($this->tournament_label_list as $tournament_label) { ?>
				<tr>

					<td><?php echo JText::_($tournament_label->id); ?>
					</td>
					<td><?php echo JText::_($tournament_label->label); ?>
					</td>
					<td><?php echo JText::_($tournament_label->description); ?>
					</td>
					<td><?php echo JText::_($tournament_label->parent_label_label); ?>
					</td>
					
					<td><?php echo '<a href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentlabels&task=edit&id={$tournament_label->id}") . '">Edit</a>'?> - <?php echo '<a class="confirm-class" href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentlabels&task=delete&id={$tournament_label->id}") . '" onclick="if (! confirm(\'Continue?\')) { return false; }">	Delete</a>' ?></td>
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
	<input type='hidden' name='controller' value='tournamentlabels' />
	<input type="hidden" name="task" value="list" />
	<input type="hidden" name="filter_order" value="<?php echo $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->direction; ?>" />
</form>
