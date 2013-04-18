<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsportevent";
$commonFrmControls = "
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentsportevent' />
";
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
  form{
  	padding: 0px;
  	margin: 0px;
  }
  .fltCtrl{
  	float: left;
  	padding: 2px;
  }
  .lbl{
  	width: 70px;
  }
  .ctrl{
 	width: 120px;
  }
  .clr{
  	clear: both;
  }
</style>

<!-- Sport -->
<div id="sportDropdown">
<form name="sportForm" action="" method="get">
<div class="fltCtrl lbl">
	<label for="sportId">
	    <?php echo JText::_( 'Sports' ); ?>:
	</label>
</div>
<div class="fltCtrl">
<select name="sportId" id="sportId" class="ctrl">
<option value="-1">Select&hellip;</option>
<?php foreach($this->sports_all as $sport) { ?>
	<option value="<?php print $sport->id; ?>"<?php print ($sport->id == $this->sport_id) ? ' selected="selected"' : ''; ?>><?php print $sport->name; ?></option>
<?php }?>
</select>
	<?=$commonFrmControls?>
	<input type="hidden" name="task" value="editSport" />
</div>
<div class="fltCtrl" style="display: none">
	<input type="submit" name="btnEditSport" value="Edit"  class="admSmlBtn"/> Or [ <a href="<?=$formAction?>&task=editSport">Create New</a> ]
</div>
<br class='clr' />
</form>
</div>

<!-- Competition -->

<div id="compDropdown">
<form name="competitionForm" action="" method="get">
<div class="fltCtrl lbl">
<label for="competitionId">
          <?php echo JText::_( 'Competition' ); ?>:
</label>
</div>
<div class="fltCtrl" id="competitionIdDiv">
<select name="competitionId" id="competitionId"  class="ctrl">
<option value="-1">Select&hellip;</option>
<?php
	if(!empty($this->sport_competitions)){
	foreach($this->sport_competitions as $competition) { ?>
	<option value="<?php print $competition->id; ?>"<?php print ($competition->id == $this->competition_id) ? ' selected="selected"' : ''; ?>><?php print $competition->name; ?></option>
<?php }
	}
?>
</select>
	<?=$commonFrmControls?>
	<input type="hidden" name="task" value="editCompetition" />
</div>
<div class="fltCtrl">
	<input type="submit" name="btnEditComp" value="Edit"  class="admSmlBtn"/> Or [ <a href="<?=$formAction?>&task=editCompetition">Create New</a> ]
</div>
<br class='clr' />
</form>
</div>

<!-- Event List -->
<hr />
<form name="adminForm" action="<?=$formAction?>" method="get" id="filterFrm">
<div id="filter">
	<div class="fltCtrl" id="sportsFilterDrp">
		Filter By:
		<select name="sportId" id="sportFilter" class="ctrl">
		<option value="-1">Sports &hellip;</option>
		<?php foreach($this->sports_all as $sport) { ?>
			<option value="<?php print $sport->id; ?>"<?php print ($sport->id == $this->sport_id) ? ' selected="selected"' : ''; ?>><?php print $sport->name; ?></option>
		<?php }?>
		</select>
	</div>
	<div class="fltCtrl" id="competitionFilterDiv">
		<select name="competitionId" id="competitionFilter" class="ctrl">
		<option value="-1">Competitions &hellip;</option>
		<?php
		if(!empty($this->sport_competitions)){
			foreach($this->sport_competitions as $competition) { ?>
			<option value="<?php print $competition->id; ?>"<?php print ($competition->id == $this->competition_id) ? ' selected="selected"' : ''; ?>><?php print $competition->name; ?></option>
		<?php }
		}
		?>
		</select>
	</div>
	<div class="fltCtrl">
		<input type="submit" name="btnFilter" value=" Filter " class="admSmlBtn" />
	</div>
	<br class='clr' />
</div>
  <div id="editcell">
    <table class="adminlist" width="100%" cellspacing="0">
      <thead>
        <tr>
          <th><?php print JHTML::_('grid.sort', JText::_('Sport'), 'sport_name', $this->direction, $this->order); ?></th>
          <th><?php print JHTML::_('grid.sort', JText::_('Competition'), 'competition_name', $this->direction, $this->order); ?></th>
          <th><?php print JHTML::_('grid.sort', JText::_('Event'), 'name', $this->direction, $this->order); ?></th>
          <th><?php print JHTML::_('grid.sort', JText::_('Start Time'), 'start_date', $this->direction, $this->order); ?></th>
          <th width='1%'>edit</th>
          <th width='1%'>delete</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $i = 0;
      if(!empty($this->event_group_list)){
	      foreach($this->event_group_list as $event_group) {
	      	$row_class = $i % 2 ? 'row1' : 'row0';
	      	$i++;     	
	 ?>
	        <tr class="<?php echo $row_class?>">

	          <td>
	            <!-- <a href="<?php print JRoute::_($formAction."&task=editSport&sportId={$event_group->tournament_sport_id}")?>"><?php print JText::_($event_group->sport_name); ?></a> -->
	            <?php print JText::_($event_group->sport_name); ?>
	          </td>
	          <td>
	          	<a href="<?php print JRoute::_($formAction."&task=editCompetition&competitionId={$event_group->tournament_competition_id}")?>"><?php print JText::_($event_group->competition_name); ?></a>
	          </td>
	          <td><?php print JText::_($event_group->name); ?></td>
	          <td><?php print JText::_($event_group->start_date); ?></td>
	          <td><?php print '<a href="' . JRoute::_($formAction."&task=editEvent&id={$event_group->id}") . '">Edit</a>'?></td>
	          <td><?php print '<a onclick="return confirm(\'Are you sure you want to delete this Event?\');" href="' . JRoute::_($formAction."&task=deleteEvent&eventId={$event_group->id}") . '">Delete</a>' ?></td>
	        </tr>
      <?php }
      }
      ?>
      </tbody>
      <tfoot>
      <tr>
        <td colspan="6">
          <?php print $this->pagination; ?>
        </td>
        </tr>
      </tfoot>
    </table>
  </div>
  <?=$commonFrmControls?>
  <input type="hidden" name="task" value="list" />
  <input type="hidden" name="filter_order" value="<?php print $this->order; ?>" />
  <input type="hidden" name="filter_order_Dir" value="<?php print $this->direction; ?>" />
</form>

<script language="javascript">
window.addEvent('domready', function() {
	var url="";
	$('sportId').addEvent('change', function(e) {
		sportId = $('sportId').getProperty('value');
		url = "<?=$formAction?>&sportId="+sportId+"&task=loadCompetitions";
		loadOptions(url,'competitionId');
	});
	//-- filter sport dropdown
	$('sportFilter').addEvent('change', function(e) {
		sportId = $('sportFilter').getProperty('value');
		url = "<?=$formAction?>&sportId="+sportId+"&task=loadCompetitions";
		loadOptions(url,'competitionFilter',"Competitions...");
	});
	// -- Submit form on change event
	$('competitionFilter').addEvent('change', function(e) {
	    $('filterFrm').submit();
	});
});

</script>