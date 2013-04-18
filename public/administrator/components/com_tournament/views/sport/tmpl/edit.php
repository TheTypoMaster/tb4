<?php
defined('_JEXEC') or die();

JToolBarHelper::save();
JToolBarHelper::cancel();
?>
<style type="text/css">
	#adminForm table {width:90%;margin: 0 5%;}
	#adminForm table thead th {text-align:left;}
	#adminForm table td {vertical-align:top;}
	legend {font-size:large;font-weight:bold;}
	<?php foreach($this->style_list as $field): ?>
	#<?php print $field; ?> {border:red 1px solid;}
	<?php endforeach;?>
</style>
<form action="index.php?option=com_tournament&amp;controller=sport" method="post" name="adminForm" id="adminForm">
	<table class="adminForm" border="0" cellpadding="10px" cellspacing="5px">
		<thead>
			<tr>
				<th colspan="2"><?php print JText::_('Sport Details'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><label for="name">Sport Name</label></td>
				<td><input type="text" name="name" id="name" value="<?php print $this->default_list['name']; ?>" /></td>
			</tr>
			<tr>
				<td><label for="description">Description</label></td>
				<td><textarea name="description" id="description"><?php print $this->default_list['description']; ?></textarea></td>
			</tr>
			<tr>
				<td><label for="external_sport_id">External ID</label></td>
				<td>
					<select name="external_sport_id" id="external_sport_id">
					<?php foreach($this->external_sport_list as $external_id => $external_name): ?>
						<option value="<?php print $external_id; ?>"><?php print $external_name; ?></option>
					<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="status_flag">Status</label></td>
				<td>
					<input type="radio" name="status_flag" id="status_flag_on" />
					<input type="radio" name="status_flag" id="status_flag_off" />
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="id" value="<?php print $this->sport->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>