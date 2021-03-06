<?php
	defined('_JEXEC') or die();

	JToolBarHelper::addNew('edit');
?>
<style type="text/css">
table.adminList {width:100%;}
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
<form name="adminForm" action="<?php print $this->form_action; ?>" method="post">
	<table class="adminList" cellpadding="0" cellspacing="0" border="0">
		<thead>
			<tr>
				<th><?php print JHTML::_('grid.sort', JText::_('NUM'), 'id', $this->direction, $this->order); ?></th>
				<th><?php print JHTML::_('grid.sort', JText::_('Sport Name'), 'name', $this->direction, $this->order); ?></th>
				<th><?php print JText::_('Description'); ?></th>
				<th><?php print JHTML::_('grid.sort', JText::_('Status'), 'status_flag', $this->direction, $this->order); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($this->sport_list as $sport): ?>
			<tr>
				<td><?php print JText::_($sport->id); ?></td>
				<td><a href="<?php print $sport->edit_link; ?>"><?php print JText::_($sport->name); ?></a></td>
				<td><?php print JText::_($sport->description); ?></td>
				<td><?php print JText::_($sport->status); ?></td>
				<td><a href="<?php print $sport->edit_link; ?>">Edit</a></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<input type="hidden" name="task" value="listview" />
	<input type="hidden" name="filter_order" value="<?php print $this->order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php print $this->direction; ?>" />
</form>