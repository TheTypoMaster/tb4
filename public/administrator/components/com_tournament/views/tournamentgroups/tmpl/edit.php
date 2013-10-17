<?php
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
?>

<div class="col width-60">
	<fieldset class="adminform">
		<legend>Tournament Group Details</legend>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class='admintable'>
				<tr>
					<td class='key'>Tournament Group ID</td>
					<td>
						<?php echo $this->escape($this->tournament_group_details['id'])?>
					</td>
				</tr>
				<tr>

					<td class='key'><label for="group">Group</group></td>
					<td><input type="text" name="group" id="group" size="64"
						value="<?php echo $this->tournament_group_details['group_name']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='key'><group for="description">Description</group></td>
					<td><input type="text" name="description" id="descritopn" size="64"
						value="<?php echo $this->tournament_group_details['description']; ?>"
						<?php echo $this->disabled;?> /></td>

				</tr>

				<tr>


					<td class='key'><group for="parent_group_id">Parent Group ID</group></td>
					<td><select name=parent_group_id id="parent_group_id">
							<?php foreach($this->groups_option_list  as $tournament_group_id => $tournament_group_group): ?>
							<option value="<?php echo $tournament_group_id; ?>"
								<?php echo $this->group_selected_list[$tournament_group_id]; ?>><?php echo $tournament_group_group; ?></option>
							<?php endforeach; ?>
					</select></td>
				</tr>


			</table>
			<div class="clr"></div>
			<input type="hidden" name="option" value="com_tournament" /> <input
				type="hidden" name="controller" value="tournamentgroups" /> <input
				type="hidden" name="id"
				value="<?php echo $this->tournament_group_details['id']; ?>" /> <input
				type="hidden" name="task" value="" />
		</form>
	</fieldset>
</div>