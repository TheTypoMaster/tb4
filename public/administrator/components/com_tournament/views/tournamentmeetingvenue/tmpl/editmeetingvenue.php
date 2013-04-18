<?php
// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="col width-60">
	<fieldset class="adminform">
		<legend>Venue Details</legend>
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<table class='admintable'>
				<tr>
					<td class='key'>Venue</td>
					<td>
						<?php echo $this->escape($this->venue->name) ?>
					</td>
				</tr>
				<tr>
					<td class='key'>State</td>
					<td>
						<select name="stateId">
								<option value="">Select &hellip;</option>
							<?php foreach($this->state_list as $state) { ?>
								<option value="<?php echo $state->id; ?>"<?php echo ($state->id == $this->formData['state']) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($state->name); ?></option>
							<?php }?>
						</select>
						OR <sup>NEW</sup>
						<input type="text" name="new_state" value="<?php echo isset($this->formData['new_state']) ? $this->escape($this->formData['new_state']) : '' ?>"></input>
						<?php echo isset($this->formErrors['state']) ? '<div class=\'error\'>' . $this->escape($this->formErrors['state']) . '</div>'  : ''?>
					</td>
				</tr>
				<tr>
					<td class='key'>Territory</td>
					<td>
						<select name="territoryId">
								<option value="">Select &hellip;</option>
							<?php foreach($this->territory_list as $territory) { ?>
								<option value="<?php echo $territory->id; ?>"<?php echo ($territory->id == $this->formData['territory']) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($territory->name); ?></option>
							<?php }?>
						</select>
						OR <sup>NEW</sup>
						<input type="text" name="new_territory" value="<?php echo isset($this->formData['new_territory']) ? $this->formData['new_territory'] : '' ?>"></input>
						<?php echo isset($this->formErrors['territory']) ? '<div class=\'error\'>' . $this->escape($this->formErrors['territory']) . '</div>'  : ''?>
					</td>
				</tr>
			</table>
			<div class="clr"></div>
			<input type="hidden" name="option" value="com_tournament" />
			<input type="hidden" name="controller" value="tournamentmeetingvenue" />
			<input type="hidden" name="id" value="<?php echo $this->venue->id; ?>" />
			<input type="hidden" name="task" value="" />
		</form>
	</fieldset>
</div>