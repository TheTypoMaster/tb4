<?php
defined('_JEXEC') or die('Restricted access');
$formAction = "index.php?option=com_tournament&controller=tournamentsportoffer";
$commonFrmControls = "
	<input type='hidden' name='option' value='com_tournament' />
	<input type='hidden' name='controller' value='tournamentsportoffer' />
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


<!-- Event List -->
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
					<th><?php print JHTML::_('grid.sort', JText::_('Match'), 'name', $this->direction, $this->order); ?></th>
					<th><?php print JHTML::_('grid.sort', JText::_('Start Time'), 'start_date', $this->direction, $this->order); ?></th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
			if(!empty($this->match_list)){
				$i = 1;
				foreach($this->match_list as $match) { ?>
					<tr class="<?php echo $i % 2 == 0 ? 'row1' : 'row0'?>">

						<td>
						<?php print JText::_($match->sport_name); ?>
						</td>
						<td>
							<?php print JText::_($match->competition_name); ?>
						</td>
						<td><?php print JText::_($match->name); ?></td>
						<td><?php print JText::_($match->start_date); ?></td>
						<?php if($match->started || $match->is_resulted):
								$link_text = $match->is_resulted ? 'View' : 'Result'; ?>
						<td><?php print '<a href="' . JRoute::_($formAction."&task=resultMatch&match_id={$match->id}") . '">'.$link_text.'</a>'?></td>
						<?php else: ?>
						<td><?php print '<a href="' . JRoute::_($formAction."&task=editOdds&match_id={$match->id}") . '">Edit Odds</a>'?></td>
						<?php endif; ?>
					</tr>
			<?php 
					$i++;
				}
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
var ajaxURL = "index.php?option=com_tournament&controller=tournamentsportevent";

window.addEvent('domready', function() {
	//-- filter sport dropdown
	$('sportFilter').addEvent('change', function(e) {
		sportId = $('sportFilter').getProperty('value');
		url = ajaxURL + "&sportId="+sportId+"&task=loadCompetitions";
		loadOptions(url,'competitionFilter',"Competitions...");
	});
	// -- Submit form on change event
	$('competitionFilter').addEvent('change', function(e) {
			$('filterFrm').submit();
	});
});

</script>