<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsportevent";
$commonFrmControls = '
	<input type="hidden" name="option"  value="com_tournament" />
	<input type="hidden" name="controller" value="tournamentsportevent" />';
/**
 * Generating a match array to check the feed external matches to remove them if they are already added in this match
 */
$event_match_list = array();
if(!empty($this->match_list)){
	foreach ($this->match_list as $match){
		$event_match_list[] = $match->external_event_id;
	}
}
?>
<style>
.tblBrd
{
	border-collapse:collapse;
}
.tblBrd, .tblBrd td, .tblBrd th
{

}
#element-box{
	line-height: 1.6;
}
#eventFrm div{
	padding: 5px 0px;
}
.req{
	color: red;
	vertical-align: super;
}
.caps{
	text-transform:uppercase;
	font-weight: bold;
}
.disabled
{
	background-color: #eee;
}
.flt{
	float: left;
}
.clear{
	clear: both;
}
.dp_container{
	float: left;
}
.lftTxt{
	text-align: left;
}

thead th, tfoot td {
    background-color: #EEEEEE;
}
thead th {
    text-align: left;
}
.key {
    background-color: #EEEEEE;
    color: #666666;
    font-weight: bolder;
    margin-right: 10px;
    padding: 3px;
    width: 175px;
}
#extMatchs{
	margin: 1px;
}
.extraSpace{
	padding: 4px;
}
</style>

<form method="get" name="adminForm" action="<?=$formAction?>" >

<table cellpadding="1" cellspacing="1">
	<tr>
		<td class='key'>Parent Sport:</td>
		<td>
			<select name="sportId" id="sportId" class="ctrl">
			<option value="-1">Select&hellip;</option>
			<?php
			foreach($this->sports_all as $sport) { ?>
				<option value="<?php print $sport->id; ?>"<?php echo (!is_null($this->event_group_data) && $sport->id == $this->event_group_data->tournament_sport_id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($sport->name); ?></option>
			<?php }?>
			</select>
			<span class='req'>*</span>
		</td>
	</tr>

	<tr>
		<td class='key'>Parent Competition:</td>
		<td>
			<select name="competitionId" id="competitionId" class="ctrl">
			<option value="-1">Select&hellip;</option>
			<?php foreach($this->competitions as $competition) { ?>
				<option value="<?php print $competition->id; ?>"<?php echo ($competition->id == $this->event_group_data->tournament_competition_id) ? ' selected="selected"' : ''; ?>><?php echo $this->escape($competition->name); ?></option>
			<?php }?>
			</select>
			<span class='req'>*</span>
		</td>
	</tr>

	<tr>
		<td class='key'>Event Name:</td>
		<td><input name="eventName" id="eventName" value="<?php echo is_null($this->event_group_data) ? '' : $this->escape($this->event_group_data->name); ?>" /> <span class='req'>*</span></td>
	</tr>

</table>

	<?=$commonFrmControls?>
	<input type="hidden" name="eventId" id="eventId" value="<? echo $this->event_group_data->id; ?>" />
	<input type="hidden" name="task" value="saveEvent" />
	<input type="hidden" name="totalTournaments" value="<?=$this->total_tournaments?>" />
	<input type="hidden" name="boxchecked" value="1" />
<div>
<?php
$displayNone = '';
if(empty($this->event_group_data->id) || $this->betting_started){
	$displayNone = "style = 'display:none'";
}
?>
<div id="extMatchs" <?=$displayNone?>>
	<span class='key' style="margin-right: 0px;">Available matches from the feed:</span>
	<select name="externalMatchs" id="externalMatchs">
		<option value="-1">Select&hellip;</option>
	<?
	if(!empty($this->ext_match_data)){
		foreach ($this->ext_match_data as $match){

			if(!in_array($match['ext_match_id'], $event_match_list) && strtotime($match['start_date']) > time()){
			?>
				<option value="<?=$match['meeting_id']?>_*_<?=$match['ext_match_id']?>"><?=$match['event_name']?></option>
			<?
			}
		}
	}
	?>
	</select>
	<input type='button' name='btnAddMatch' id='btnAddMatch' value='Add Match' />
	<?
	if(sizeof($event_match_list) > 0){ ?>
		<input type='button' name='btnReimportMarket' id='btnReimportMarket' value='Reimport Market' />
	<? } ?>
</div>
<?
if($this->total_tournaments){
?>
	<div class='caps'>Note: This event has [<?=$this->total_tournaments?>] tournaments assigned to it.</div>
<?
}
?>
<!-- The existing match list -->
<?
$total_match = 0;
if(!empty($this->match_list)){
?>
<div class="extraSpace"></div>
<fieldset>
<legend>Matches for this event</legend>
	<div id="matchList">
		<table cellpadding="3" cellspacing="1" class="tblBrd">
		<thead>
			<tr>
				<th class="lftTxt">Match</th>
				<th>Start Date [yyyy-mm-dd] [hh:mm:ss]</th>
				<th>Action</th>
			</tr>
		</thead>
<?
	foreach ($this->match_list as $match){
		$match_date_arr = explode(" ", $match->start_date);
?>
			<tr>
				<td><?=$match->name?></td>
				<td><input type="hidden" value="<?php echo $match->id?>" name="matchIds[]" />
				<input type="text" value="<?php echo $match_date_arr[0]?>" name="matchStartDate[]" size="12" Readonly />
				<input type="text" value="<?php echo $match_date_arr[1]?>" name="matchStartTime[]" size="8" /></td> 
				<td><a href="<?=$formAction?>&task=deleteMatch&matchId=<?=$match->id?>&eventId=<?=$this->event_group_data->id?>">remove</a></td>
			</tr>
		<?
		$total_match ++;
		/**
		 * generating an array of the existing matchs to check with the feed droupdown
		 */

	}
	?>
		</table>
	</div>
</fieldset>
<?}?>

</div>
<div style="padding-left: 4px;">
	<div <?=$displayNone?> style="padding-top: 5px"><b>Or if no matchs are available...</b></div>
	<div class="flt key">Event Start Date</div>
	<div class="dp_container"><input type="text" value="<?php echo is_null($this->event_group_data) ? '' : $this->event_group_data->start_date?>" name="eventStartDate" id="eventStartDate" alt="{format:'yyyy-mm-dd',yearStart:2010}" <?php echo $total_match ? 'class="disabled"':' class="DatePicker"'?> readonly="readonly" /> <span class='req'>*</span></div>
	<div class="clear"></div>

</div>
<?php
if($total_match > 0){

?>
<div class="extraSpace"></div>
<fieldset>
<legend>Default set of Bet Types</legend>
<div id="betTypList">
<?php if (empty($this->market_list)) : ?>
	<?php if ($total_match == 1) : ?>
	The match doesn't bet types available
	<?php else : ?>
	There are no common bet types for the matches above
	<?php endif; ?>
<?php else : ?>
	<table cellpadding="3" cellspacing="1" class="tblBrd">
		<thead>
		<tr>
			<th class="lftTxt">Bet Type</th>
			<th class="lftTxt">Line</th>
			<th class="lftTxt">Description</th>
			<th>Status</th>
		</tr>
		</thead>
		<?php
	
		foreach($this->market_list as $market){
			$checked = "";
			if(in_array($market['id'], (array)$this->bet_type_list)) $checked = 'checked="checked"';
		?>
		<tr>
			<td><?=$market['name']?></td>
			<td> (<?=$market['line']?>)</td>
			<td><?=$market['description']?></td>
			<td><input type="checkbox" name="betTypes[]" value="<?=$market['id']?>" <?=$checked?> /></td>
		</tr>
		<?php
		}
		?>
	</table>
<?php endif; ?>
</div>
</fieldset>
<?php }?>
<!-- the following controls to be used only for validation purpose  -->
<input type="hidden" id="orgCompetitionId" value="<? echo $this->event_group_data->tournament_competition_id; ?>" />
<input type="hidden" id="totMatchs" name="totMatchs" value="<?=$total_match?>" />
</form>


<form name="eventFrm" id="eventFrm">
	<?=$commonFrmControls?>
	<input type="hidden" name="matchEventId" id="matchEventId" value="<? echo $this->event_group_data->id; ?>" />
	<input type="hidden" name="matchCompetitionId" id="matchCompetitionId" value="<? echo $this->event_group_data->tournament_competition_id; ?>" />
	<input type="hidden" name="extMatchInfo" id="extMatchInfo" value="" />
	<input type="hidden" name="extMatchName" id="extMatchName" value="" />
	<input type="hidden" name="task" value="saveMatch" />
</form>



<form name="reimportFrm" id="reimportFrm">
	<?=$commonFrmControls?>
	<input type="hidden" name="eventId" id="eventId" value="<? echo $this->event_group_data->id; ?>" />
	<input type="hidden" name="task" value="reimportMarkets" />
</form>
<script language="javascript">

window.addEvent('domready', function() {
	$('sportId').addEvent('change', function(e) {
		sportId = $('sportId').getProperty('value');
		url = "<?=$formAction?>&sportId=" + sportId + "&task=loadCompetitions";
		loadOptions(url, "competitionId");
	});
	$('competitionId').addEvent('change', function(e) {
		competitionId = $('competitionId').getProperty('value');
		url = "<?=$formAction?>&competitionId=" + competitionId + "&task=loadExtMatchs";
		loadOptions(url, "externalMatchs");
	});
	if($('btnAddMatch')){
		$('btnAddMatch').addEvent('click', function(e) {
			eventId = $('eventId').getProperty('value');

			if(eventId > 0) {
				if($('competitionId').getProperty('value') != $('orgCompetitionId').getProperty('value'))
				{
					alert("You cannot change competition as there are matches in this event.");
				}
				else{
					document.getElementById('extMatchInfo').value = $('externalMatchs').getProperty('value');
					$('eventFrm').submit();
				}
			}
			else{
				alert("You cannot add match unless you save the event first.");
			}
		});
	}

	if($('btnReimportMarket')){
		$('btnReimportMarket').addEvent('click', function(e) {
			$('reimportFrm').submit();
		});
	}
});
</script>