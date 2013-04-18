<?php
defined('_JEXEC') or die('Restricted access');
?>
<form method="post" name="adminForm">

<table cellpadding="" cellspacing="">
	<tr>
		<td class='key'>Sport display name:</td>
		<td><input name="sportName" id="sportName" value="<? echo is_null($this->sports_data) ? '' : $this->sports_data->name; ?>" /></td>
	</tr>
	<tr>
		<td class='key'>Feed ID:</td>
		<td>
			<select name="externalSportId" id="externalSportId">
				<option value="-1">Select&hellip;</option>
			<?
				foreach ($this->ext_sport_data as $sport){
					$selected='';
					if($this->sports_data->external_sport_id == $sport['ext_sport_id']) $selected="selected";
					echo "<option value='".$sport['ext_sport_id']."' $selected>".$sport['ext_sport_name']."</option>";
				}
			?>
			</select>
		</td>
	</tr>
</table>
<input type="hidden" name="option" value="com_tournament" />
	<input type="hidden" name="controller" value="tournamentsportevent" />
	<input type="hidden" name="sportId" value="<? echo $this->sports_data->id; ?>" />
	<input type="hidden" name="task" value="saveSport" />	
</form>