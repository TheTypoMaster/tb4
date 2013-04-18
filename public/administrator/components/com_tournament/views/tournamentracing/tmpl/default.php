<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentracing";
?>
<style type="text/css">
thead th, tfoot td {
	background-color:#eee;
	padding:5px;
}

thead th {
	text-align:left;
}

tbody td {
	padding:2px;
}

tfoot td {
	text-align:center;
}
.subNav {
	padding: 5px;
}
.selLnk{
	font-weight: bold;
	text-decoration: underline !important;
	color: #000 !important;
}
</style>


<div class="subNav">
	<a href="<?=$formAction?>&private=0" class="<?php echo $this->tournament_type ? '' : 'selLnk' ?>" >Public Tournament</a> | <a href="<?=$formAction?>&private=1"  class="<?php echo $this->tournament_type ? 'selLnk' : '' ?>" >Private Tournament</a>
</div>
<form name="adminForm" action="index.php?option=com_tournament&amp;controller=tournamentracing" method="post">
	<div id="editcell">
		<table class="adminList" width="100%" cellspacing="0">
			<thead>
				<tr>
					<th><?php print JHTML::_('grid.sort', JText::_( 'NUM' ), 't.id', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Tournament Name'), 't.name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Parent Name'), 't2.name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Meeting Name'), 'm.name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Type/Sport'), 't.tournament_sport_id', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Start Time'), 't.start_date', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('End Time'), 't.end_date', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Prize Formula'), 't.jackpot_flag', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Game Play'), 't.jackpot_flag', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Buy-in'), 't.buy_in', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Entry-fee'), 't.entry_fee', $this->direction, $this->order); ?></th>
					<th><?php print JText::_('Entrants'); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Status'), 't.status_flag', $this->direction, $this->order); ?></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($this->tournament_list as $tournament) { ?>
				<tr>
					<td><?php print JText::_($tournament->id); ?></td>
					<td>
						<a href="index.php?option=com_tournament&amp;controller=tournamentracing&amp;task=edit&amp;id=<?php print $tournament->id; ?>"><?php print JText::_($tournament->name); ?></a>
					</td>
					<td><?php print JText::_($tournament->parent_name); ?></td>
					<td><?php print JText::_($tournament->meeting_name); ?></td>
					<td><?php print JText::_($tournament->sport_name); ?></td>
					<td><?php print JText::_($tournament->start_date); ?></td>
					<td><?php print JText::_($tournament->end_date); ?></td>
					<td><?php print JText::_($tournament->prize_formula); ?></td>
					<td><?php print JText::_($tournament->gameplay); ?></td>
					<td><?php print JText::_($tournament->buy_in); ?></td>
					<td><?php print JText::_($tournament->entry_fee); ?></td>
					<td><?php print JText::_($tournament->entrants); ?></td>
					<td><?php print JText::_($tournament->status . ' (' . $tournament->cancelled . ')'); ?></td>
					<td><?php print '<a href="' . JRoute::_("index.php?option=com_tournament&controller=tournament&task=view&id={$tournament->id}") . '">View</a>'?></td>
					<td><?php $this->tournament_type ? '' : print '<a href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentracing&task=cloneTournament&id={$tournament->id}") . '">Clone</a>'?></td>
					<td><?php print '<a href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentracing&task=cancelform&id={$tournament->id}") . '">Cancel</a>'?></td>
					<td><?php print '<a onclick="return confirm(\'Are you sure you want to delete this tournament?\');" href="' . JRoute::_("index.php?option=com_tournament&controller=tournamentracing&task=delete&id={$tournament->id}") . '">Delete</a>' ?></td>
				</tr>
			<?php } ?>
			</tbody>
			<tfoot>
				<td colspan="17">
					<?php print $this->pagination; ?>
				</td>
			</tfoot>
		</table>
	</div>
	<input type="hidden" name="option" value="com_tournament" />
	<input type="hidden" name="controller" value="tournamentracing" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php print $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php print $this->direction; ?>" />
</form>