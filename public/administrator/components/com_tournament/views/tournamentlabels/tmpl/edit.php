<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>

<div class="col width-60">
	<fieldset class="adminform">
		<legend>Tournament Label Details</legend>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class='admintable'>
				<tr>
					<td class='key'>Tournament Label ID</td>
					<td>
						<?php echo $this->escape($this->tournament_label_details['id'])?>
					</td>
				</tr>
				<tr>

					<td class='key'><label for="label">Label</label></td>
					<td><input type="text" name="label" id="label" size="64"
						value="<?php echo $this->tournament_label_details['label']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='key'><label for="description">Description</label></td>
					<td><input type="text" name="description" id="descritopn" size="64"
						value="<?php echo $this->tournament_label_details['description']; ?>"
						<?php echo $this->disabled;?> /></td>

				</tr>

				<tr>


					<td class='key'><label for="parent_label_id">Parent Label ID</label></td>
					<td><select name=parent_label_id id="parent_label_id">
							<?php foreach($this->labels_option_list  as $tournament_label_id => $tournament_label_label): ?>
							<option value="<?php echo $tournament_label_id; ?>"
								<?php echo $this->label_selected_list[$tournament_label_id]; ?>><?php echo $tournament_label_label; ?></option>
							<?php endforeach; ?>
					</select></td>
				</tr>
				
				<tr>
				<td class='key'>
						<label for="label_group_id">Assign Label to Groups</label>
					</td>
					
					
					<td>
					
						<select name="label_group_id[]" id="label_group_id"  multiple="multiple" size ='5'>
							<?php foreach($this->label_groups_option_list as $label_group_id => $label_group_name): ?>
							<option value="<?php echo $label_group_id; ?>"
								<?php echo in_array($label_group_id, $this->label_groups_selected_list) ? 'selected="selected"' : ''?>><?php echo $label_group_name; ?></option>
							<?php endforeach; ?>
						</select>
						</td>
				</tr>


			</table>
			<div class="clr"></div>
			<input type="hidden" name="option" value="com_tournament" /> <input
				type="hidden" name="controller" value="tournamentlabels" /> <input
				type="hidden" name="id"
				value="<?php echo $this->tournament_label_details['id']; ?>" /> <input
				type="hidden" name="task" value="" />
		</form>
	</fieldset>
</div>