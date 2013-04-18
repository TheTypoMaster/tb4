<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsport";
?>
<style type="text/css">
label {
	display: block;
	width: 150px;
	margin-right: 10px;
	background-color: #eee;
	float: left;
	padding: 5px;
	font-weight: bolder;
	color: #666;
}

div.input-field {
	clear: both;
	padding: 5px;
}

div.input-field p {
	clear: both;
	padding: 5px 0 0 5px;
}
.disabled
{
	background-color: #eee;
}
</style>
<?php
	$privateLabel 	= $this->tournament_data->private_flag ? 'Private ' : '';
	$bettingStarted = $this->betting_started ? ' disabled="disabled" class="disabled"' : '';
	$disabled 		= $this->entrants ? ' disabled="disabled" class="disabled"' : '';
	$privateDetails = $this->tournament_data->private_flag ? '' : "style='display:none'";
	if(!empty($this->tournament_data->jackpot_flag)){
		$jackpot =  'checked="checked" ';
	}
	else{
		$showParent = "style='display:none;'";
	}
	if($this->tournament_data->status_flag || !$this->tournament_data->id) $statusFlag = ' checked="checked"';
?>
<form action="<?=$formAction?>" method="post" name="adminForm" id="adminForm">
<div class="col50">
<fieldset id="tournament-information">
<legend><?php echo JText::_( $privateLabel . 'Tournament Details' ); ?></legend>

<?php if ($this->tournament_data->id) : ?>
<div class="input-field">
	<label for="name"> <?php echo JText::_( 'Tournament Name' ); ?>:</label>
	<input type="text" name="name" id="name" size="32" maxlength="250" value="<?=$this->tournament_data->name?>" />
</div>
<?php endif ?>

<div class="input-field">
	<label for="sportId"> <?php echo JText::_( 'Sport' ); ?>:</label>
	<select name="sportId" id="sportId" <?=$disabled?>>
		<option value="-1">Select&hellip;</option>
		<?php
		foreach($this->sports_all as $sport) { ?>
		<option value="<?php print $sport->id; ?>"
		<?php print ($sport->id == $this->tournament_data->tournament_sport_id) ? ' selected="selected"' : ''; ?>><?php print $sport->name; ?></option>
		<?php }?>
	</select>
</div>
<div class="input-field">
	<label for="competitionId"> <?php echo JText::_( 'Competition' ); ?>:</label>
	<select name="competitionId" id="competitionId" <?=$disabled?>>
		<option value="-1">Select&hellip;</option>
		<?php foreach($this->competitions as $competition) { ?>
		<option value="<?php print $competition->id; ?>"
		<?php print ($competition->id == $this->tournament_data->tournament_competition_id) ? ' selected="selected"' : ''; ?>><?php print $competition->name; ?></option>
		<?php }?>
	</select>
</div>

<div class="input-field">
	<label for="eventId"> <?php echo JText::_( 'Event' ); ?>:</label>
	<select name="event_id" id="eventId" <?=$disabled?>>
		<option value="-1">Select&hellip;</option>
		<?php foreach($this->events_all as $event) { ?>
		<option value="<?php print $event->id; ?>"
		<?php print ($event->id == $this->tournament_data->tournament_event_id) ? ' selected="selected"' : ''; ?>><?php print $event->event_name; ?></option>
		<?php }?>
	</select>
</div>
<div class="input-field">
	<label for="tournament_value"> <?php echo JText::_( 'Tournament Value' ); ?>:</label>
	<select name="tournament_value" id="tournament_value"<?php echo $disabled;?>>
		<option value="-1">Select&hellip;</option>
		<?php
		foreach($this->buyin_list as $buyin) {
			$selected = ($buyin->id == $this->current_buy_in->id) ? ' selected="selected"' : '';
			?>
		<option <?php print $selected; ?> value="<?php print $buyin->id; ?>"><?php print 'Buy-in: ' . $buyin->buy_in . ', Entry-fee: ' . $buyin->entry_fee; ?></option>
		<?php }?>
	</select>
</div>
<div class="input-field"><label for="jackpot_flag"> <?php echo JText::_( 'Jackpot' ); ?>:</label>
	<input name="jackpot_flag" id="jackpot_flag" type="checkbox" value="1" <?=$jackpot?> <?=$disabled;?> />
</div>
<div class="input-field" id="jackpot_parent" <?=$showParent?>>
	<label for="parent_tournament_id"> <?php echo JText::_( 'Jackpot Parent' ); ?>:</label>
	<select name="parent_tournament_id" id="parent_tournament_id" <?=$disabled?>>
		<option value="-1">Select&hellip;</option>
		<?php foreach($this->jackpot_parent_list as $parent) { ?>
		<option value="<?php print $parent->id; ?>"
		<?php print ($parent->id == $this->tournament_data->parent_tournament_id) ? ' selected="selected"' : ''; ?>><?php print $parent->name; ?></option>
		<?php }?>
	</select>
</div>
<div class="input-field">
	<label for="start_currency"> <?php echo JText::_( 'Starting Bucks' ); ?>:</label>
	<input type="text" name="start_currency" id="start_currency" size="10" maxlength="15" value="<?php print (is_null($this->tournament_data->start_currency)) ? 1000 : $this->tournament_data->start_currency / 100; ?>" <?php echo $disabled;?> />
</div>
<div class="input-field">
	<label for="minimum_prize_pool"> <?php echo JText::_( 'Minimum Prize Pool' ); ?>: </label>
	<input type="text" name="minimum_prize_pool" id="minimum_prize_pool" size="10" maxlength="10"	value="<?php print (is_null($this->tournament_data->minimum_prize_pool)) ? 10 : $this->tournament_data->minimum_prize_pool / 100; ?>" <?php echo $disabled;?> />
</div>
<?php if ($this->tournament_data->id) : ?>
<div class="input-field">
	<label for="description">
		<?php echo JText::_( 'Tournament Information' ); ?>:
	</label>
	<textarea name="description" id="description"><?php print $this->tournament_data->description; ?></textarea>
</div>
<?php endif; ?>

<div class="input-field">
	<label for="allBettingClosed"> <?php echo JText::_( 'All Betting Closed' ); ?>:</label>
	<?php
		$selFirst = $selLast = "selected = selected";
		if($this->tournament_data->closed_betting_on_first_match_flag == 1) $selLast ='';
		else $selFirst = '';
	?>
	<select name="all_betting_closed" id="allBettingClosed" <?=$disabled?>>
		<option value="1" <?=$selFirst?>>Time of 1st Match</option>
		<option value="0" <?=$selLast?>>Time of last Match</option>
	</select>
</div>

<div class="input-field">
	<label for="reinvest_winnings_flag"> <?php echo JText::_( 'Allow reinvest winnings' ); ?>:</label>
	<?
		if($this->tournament_data->id && $this->tournament_data->reinvest_winnings_flag < 1) $reinvest_checked = '';
		else $reinvest_checked = ' checked="checked"';
	?>
	<input type="checkbox" value="1" name="reinvest_winnings_flag" id="reinvest_winnings_flag" <?=$reinvest_checked?> <?= $disabled ?>/>
</div>

<div class="input-field">
	<label for="status_flag"> <?php echo JText::_( 'Publish Status' ); ?>:</label>
	<input type="checkbox" value="1" name="status_flag" id="status_flag" <?=$statusFlag?> <?=$disabled;?> />
</div>
</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->tournament_data->id; ?>" />
	<input type="hidden" name="private_flag" value="<?php echo $this->tournament_data->private_flag; ?>" />
	<input type="hidden" name="entrants" value="<?=$this->entrants?>" />
	<input type="hidden" name="task" value="" />
</div>
</form>

<script language="javascript">
var ajaxURL = "index.php?option=com_tournament&controller=tournamentsportevent";
window.addEvent('domready', function() {

	$('sportId').addEvent('change', function(e) {
		sportId = $('sportId').getProperty('value');
		url = ajaxURL + "&sportId=" + sportId + "&task=loadCompetitions";
		loadOptions(url, "competitionId");
	});
	$('competitionId').addEvent('change', function(e) {
		competitionId = $('competitionId').getProperty('value');
		url = ajaxURL + "&competitionId=" + competitionId + "&task=loadEvents";
		loadOptions(url, "eventId");
	});
	$('jackpot_flag').addEvent('click', function(e) {
		parentChecked = $('jackpot_flag').getProperty('checked');
		if(parentChecked == true) $('jackpot_parent').style.display = "block";
		else $('jackpot_parent').style.display = "none";
	});
});
/**
 * overwrite the Joomla Onsubmit JS validation - submitbutton
 */
function submitbutton(thebutton){
	parentJackpot = true;
	parentChecked = $('jackpot_flag').getProperty('checked');

	if(parentChecked == true && $('parent_tournament_id').getProperty('value') < 1){
		parentJackpot = confirm("Did you forget to assign a parent?");
	}
	if(parentJackpot){
		submitform(thebutton); //submit form if passed validation
	}
}
</script>


