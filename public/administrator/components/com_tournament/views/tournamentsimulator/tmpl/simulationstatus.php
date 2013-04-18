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
		<legend><?php echo JText::_( 'Running simulation' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tr>
					<td width="150" class="key">
						<label for="meeting">
							<?php echo JText::_( 'Meeting' ); ?>
						</label>
					</td>
					<td>
						<?php echo  JText::_($this->meeting->name );?>
					</td>
				</tr>
				<tr>
					<td class="key">
						
					</td>
					<td>
						<input type="submit" name="submit" size="10" value="Set all races to paying" />
					</td>	
				</tr>
			</table>
		</fieldset>
	</div>
	<?php /*<div class="col width-55">
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
	</div> */?>
			<div class="clr"></div>
	<input type="hidden" name="meeting_id" value="<?php echo $this->meeting->id; ?>" />
	<input type="hidden" name="option" value="com_tournament" />
	<input type="hidden" name="task" value="update" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>