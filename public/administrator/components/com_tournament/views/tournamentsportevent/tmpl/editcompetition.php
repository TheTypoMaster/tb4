<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsportevent";
$commonFrmControls = "
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentsportevent' />
";

$disabled = '';
if($this->event_exists > 0) $disabled = 'disabled="disabled" class="disabled"';
?>
<style>
.disabled
{
	background-color: #eee;
}
</style>
<form method="post" name="adminForm">

<table cellpadding="" cellspacing="">
	<tr>
		<td class='key'>Parent Sport:</td>
		<td>
			<select name="sportId" id="sportId" <?=$disabled?>>
			<option value="-1">Select&hellip;</option>
			<?php foreach($this->sports_all as $sport) { ?>
				<option value="<?php echo $this->escape($sport->id); ?>"<?php echo ($sport->id == $this->sport_id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($sport->name); ?></option>
			<?php }?>
			</select>
		</td>
	</tr>
	<tr>
		<td class='key'>Competition Name:</td>
		<td><input name="competitionName" id="competitionName" value="<? echo is_null($this->competition_data) ? '' : $this->escape($this->competition_data->name); ?>" /></td>
	</tr>
	<tr>
		<td class='key'>Feed ID:</td>
		<td>
			<select name="externalCompetitionId" id="externalCompetitionId" <?=$disabled?>>
				<option value="-1">Select&hellip;</option>
			<?
			if(!empty($this->ext_competition_data)){
				foreach ($this->ext_competition_data as $competition){
					$selected='';
					if($this->competition_data->external_competition_id == $competition['league_id']) $selected="selected";
					echo "<option value='".$competition['league_id']."' $selected>".$competition['league_name']."</option>";
				}
			}
			?>
			</select>
		</td>
	</tr>
</table>
	<?=$commonFrmControls?>
	<input type="hidden" name="competitionId" value="<? echo $this->competition_data->id; ?>" />
	<input type="hidden" name="task" value="saveCompetition" />
	<input type="hidden" name="boxchecked" value="1" />
</form>


<script language="javascript">

window.addEvent('domready', function() {
	$('sportId').addEvent('change', function(e) {
		sportId = $('sportId').getProperty('value');
		url = "<?=$formAction?>&sportId="+sportId+"&task=loadExtCompetitions";
		loadOptions(url, "externalCompetitionId");
	});

});

</script>