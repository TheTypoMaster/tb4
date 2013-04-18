<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsimulator";
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
.notice {
	padding: 15px;
	font-size: 20px;
}
</style>
<form name="adminForm" action="<?php echo $formAction; ?>" method="post">
<div class="col width-45">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Running simulation status' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="150" class="key">
						<label for="meeting">
							<?php echo JText::_( 'Meeting' ); ?>
						</label>
					</td>
					<td>
						<select id="meeting">
							<?php foreach ($this->meeting_list as $meeting):?>
							<option value="<?php echo $meeting->id; ?>"><?php echo $meeting->name; ?></option>
							<?php endforeach;?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="template">
							<?php echo JText::_( 'Simulation Template' ); ?>
						</label>
					</td>
					<td>
						<select id="template">
							<?php foreach ($this->template_list as $id => $template):?>
							<option value="<?php echo $id; ?>"><?php echo $template; ?></option>
							<?php endforeach;?>
						</select>
					</td>	
				</tr>
				<tr>
					<td class="key">
						<label for="start_in">
							<?php echo JText::_( 'Start in' ); ?>
						</label>
					</td>
					<td>
						<input type="number" name="start_in" id="start_in" class="inputbox" size="10" value="10" /> mins
					</td>	
				</tr>
				<tr>
					<td class="key">
						<label for="compress">
							<?php echo JText::_( 'Compress meeting' ); ?>
						</label>
					</td>
					<td>
						<input type="checkbox" name="compress" id="compress" class="inputbox" checked="checked" />
					</td>	
				</tr>
				<tr>
					<td class="key">
					&nbsp;	
					</td>
					<td>
					</td>	
				</tr>
				<tr>
					<td class="key">
						
					</td>
					<td>
						<input type="submit" name="submit" size="10" value="Start simulation" />
					</td>	
				</tr>
			</table>
		</fieldset>
	</div>
	<div class="col width-55">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Last simulation' ); ?></legend>
			<table class="admintable">
				<tr>
					<td width="150" class="key">
						<label for="meeting">
							<?php echo JText::_( 'Meeting name' ); ?>
						</label>
					</td>
					<td>
							<?php echo $this->last_simulation->name; ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="finished_time">
							<?php echo JText::_( 'Finished at' ); ?>
						</label>
					</td>
					<td>
							<?php echo $this->last_simulation->finished_time; ?>
					</td>	
				</tr>
			</table>
		</fieldset>
	</div>
			<div class="clr"></div>

	<input type="hidden" name="option" value="com_tournament" />
	<input type="hidden" name="task" value="simulate" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>