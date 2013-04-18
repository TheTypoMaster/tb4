<?php
defined('_JEXEC') or die();

JToolBarHelper::save();
JToolBarHelper::cancel();
?>
<style type="text/css">
	#adminForm table {width:40%;margin: 0 5%;}
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
				<th colspan="2"><?php print JText::_('Competition Details'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><label for="tournament_sport_id">Sport</label></td>
				<td>
					<select name="tournament_sport_id" id="tournament_sport_id">
					<?php foreach($this->sport_option_list as $sport_id => $sport_name): ?>
						<option value="<?php print $sport_id; ?>"<?php print $this->sport_selected_list[$sport_id]; ?>>
							<?php print JText::_($sport_name); ?>
						</option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="external_competition_id">External Competition</label></td>
				<td>
					<select name="external_competition_id" id="external_competition_id">
					<?php foreach($this->external_competition_option_list as $external_id => $external_name): ?>
						<option value="<?php print $external_id; ?>"<?php print $this->external_competition_selected_list[$external_id]; ?>>
							<?php print JText::_($external_name); ?>
						</option>
					<?php endforeach;?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="name">Competition Name</label></td>
				<td><input type="text" name="name" id="name" value="<?php print $this->default_list['name']; ?>" /></td>
			</tr>
			<tr>
				<td><label for="status_flag">Status</label></td>
				<td>
					<input type="radio" name="status_flag" id="status_flag_on"<?php print $this->status_flag_on; ?> />
					<input type="radio" name="status_flag" id="status_flag_off"<?php print $this->status_flag_off; ?> />
				</td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="id" value="<?php print $this->competition->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>